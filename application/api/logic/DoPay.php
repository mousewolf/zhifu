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
     * @return mixed
     * @throws \Exception
     */
    public function pay($orderNo)
    {
        //检查支付状态
        $order = $this->modelOrders->checkOrderValid($orderNo);

        //创建支付预订单
        return $this->preOrder($order);
    }

    /**
     * 指定网关   [***应该分出来做成service***]
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @return array
     */
    private function preOrder($order){
        //随机支付渠道 -- 返回支付方式ID
        //传入支付方式ID
        $config = $this->modelPayChannel->getChannelMap($this->modelPayCode->getCodeId($order['channel']));

        //添加订单支付通道ID
        $this->logicOrders->setValue(['trade_no'=>$order['trade_no']],$config['id']);
        //处理类名称
        $payment = uncamelize($config['action']);
        //TODO  分发支付网关
        $result = ApiPayment::$payment($config['param'])->pay($order);

        return $result;
    }

}