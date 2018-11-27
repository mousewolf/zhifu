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
use app\common\library\exception\OrderException;
use think\Log;
use Yansongda\Pay\Pay;

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
     * 支付分发  【使用Yansongda/pay支付类库】
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
        try {
            //随机支付渠道 -- 返回支付方式ID
            $config = $this->modelPayChannel->getChannelMap($this->modelPayCode->getCodeId($order['channel']));
            //获取支付渠道
            $action = $config['action'];
            //添加订单支付通道ID
            $this->logicOrders->setValue(['trade_no' => $order['trade_no']], $config['id']);
            //分割支付方式
            $channel =  explode('.', $order['channel']);
            $payment = $channel[0];  $pay = $channel[1];
            //配置载入
            $config = array_merge(config('pay.' . $action), $config['param']);
            //构建支付数据
            $orderData = [
                'out_trade_no' => $order['trade_no'],      //平台支付单号trade_no   商户订单 out_trade_no
            ];
            switch ($payment){
                case 'wx':
                case 'qq':
                    $orderData ['total_fee'] = bcmul(100, $order['amount']);  //支付金额 分
                    $orderData ['body'] = $order['subject'];  //支付金额 分
                    break;
                case 'ali':
                    $orderData ['total_amount'] = $order['amount'];//支付金额 元
                    $orderData ['subject'] = $order['subject'];  //支付金额 分
                    break;
                //其他第三/四方自行接入  -- 推荐写在app\api\service\payment下面  继承父类ApiPayment
                //不会可以找我，付费服务
            }
            //分发
            return Pay::$action($config)->$pay($orderData);


        } catch (\Exception $e) {
            Log::error('Create Pay Order Fail:[' . $e->getMessage() . ']');
            throw new OrderException([
                'errorCode' => '200008',
                'msg' => 'Create Pay Order Fail:[ Channel Error .]'
            ]);
        }
    }

}