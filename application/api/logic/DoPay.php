<?php

/**
 *  +----------------------------------------------------------------------
 *  | 草帽支付系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */

namespace app\api\logic;
use app\api\service\ApiPayment;
use app\common\library\exception\OrderException;
use think\Exception;
use think\Log;

/**
 * 支付处理类  （优化方案：提出单个支付类  抽象类对象处理方法 便于管理）
 *
 * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
 *
 */
class DoPay extends BaseApi
{
    /**
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $orderNo
     *
     * @return mixed
     * @throws \Exception
     */
    public function pay($orderNo)
    {
        //检查支付状态
        $order = $this->modelOrders->checkOrderValid($orderNo);

        //创建支付预订单
        return $this->prePayOrder($order);
    }

    /**
     * 支付分发
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     *
     * @return mixed
     * @throws OrderException
     */
    public function prePayOrder($order)
    {
        //随机支付渠道 -- 返回支付方式ID
        $appChannelMap = $this->modelPayChannel->getChannelMap($this->modelPayCode->getCodeId($order['channel']));
        //规则参数返回
        $configMap = $this->fetchConfig($order, $appChannelMap);
        //添加订单支付通道ID
        $this->logicOrders->setValue(['trade_no' => $order['trade_no']], $configMap['id']);

        //获取支付渠道
        list($payment,$action) = str2arr($order['channel'] . '.' .  $configMap['action'],'.');

        //配置载入
        $appConfig = !empty(config('pay.' . $payment))
            ? array_merge(config('pay.' . $payment), $configMap['param'])
            : $configMap['param'];

        try {

            //支付分发
            $result = ApiPayment::$action($appConfig)->$payment($order);

            return $result;

        } catch (Exception $e) {
            Log::error('Create Pay Order Fail:[' . $e->getMessage() . ']');
            throw new OrderException([
                'errorCode' => '200008',
                'msg' => 'Create Pay Order Fail:[ Please wait for a moment to try.]'
            ]);
        }
    }

    /**
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @param $appChannelMap
     *
     * @return mixed
     * @throws OrderException
     */
    private function fetchConfig($order, $appChannelMap){

        $configMap = [];
        foreach ($appChannelMap as $key => $val){
            $timeslot = json_decode($val['timeslot'],true);

            if ($order['amount'] < $val['single']
                && strtotime($timeslot['start']) < time() && time() < strtotime($timeslot['end']) ){
                $configMap[] = $val;
            }
        }
        if (!empty($configMap)){
            $key = array_rand($configMap);
            return  [
                'id'=>  $configMap[$key]['id'],
                'action'=> $configMap[$key]['action'],
                'param'=>json_decode($configMap[$key]['param'],true)
            ];
        }
        Log::error('暂无可用渠道，请稍后尝试');
        throw new OrderException([
            'msg' => '暂无可用渠道，请稍后尝试'
        ]);
    }
}