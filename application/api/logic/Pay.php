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

use think\Log;
use Yansongda\Pay\Pay as Service;

/**
 * 支付处理类  （优化方案：提出单个支付类  抽象类对象处理方法 便于管理）
 *
 * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
 *
 */
class Pay extends BaseApi
{
    /**
     * 勇敢的小笨羊
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $orderNo
     * @return \Symfony\Component\HttpFoundation\Response|\Yansongda\Supports\Collection
     */
    public function pay($orderNo)
    {
        //检查支付状态
        $order = $this->modelOrders->checkOrderValid($orderNo);

        //写入订单超时队列
        Log::notice('写入订单超时队列');
        $this->logicQueue->pushJobDataToQueue('AutoOrderClose' , $order , 'AutoOrderClose');

        //创建支付预订单
        return $this->preOrder($order);
    }

    /**
     * 指定网关   [***应该分出来做成service***]
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @return mixed|\Symfony\Component\HttpFoundation\Response|\Yansongda\Supports\Collection
     */
    private function preOrder($order){
        //传入支付方式码号 wx_scan  获取支付商户
        //'id' => 100006,
        //'param' =>
        //  'mchid' => '1493758822'
        //  'appid' => 'wx1c32cda245563ee1'
        //  'key' => '06c56xxxxxxx7b6db'
        $config = $this->modelChannel->getChannelMap($order['channel']);
        //添加订单支付通道ID
        $this->logicOrders->setValue(['trade_no'=>$order['trade_no']],$config['id']);
        //判断支付方式
        switch ($order['channel']){
            case 'ali_scan':
            case 'ali_web':
            case 'ali_wap':
            case 'ali_app':
                Log::notice('支付宝支付');
                $result = $this->makeAliOrder($order,$config['param']);
                break;
            case 'wx_scan':
            case 'wx_h5':
            case 'wx_app':
                Log::notice('微信支付');
                $result = $this->makeWxOrder($order,$config['param']);
                break;
            case 'qq_scan':
                $result = $this->makeQpayOrder($order,$config['param']);
                break;
            default:
                $result = $this->makeWxOrder($order,$config['param']);
                break;
        }
        return $result;
    }

    /**
     * 发起支付宝支付请求
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @param $config
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function makeAliOrder($order,$config){

        $orderData = [
            'out_trade_no' => $order['trade_no'],     //平台支付单号
            'total_amount' => $order['amount'],   //支付金额
            'subject'      => $order['subject'],  //支付项目
        ];

        //是否手机端
        if(request()->isMobile()){
            $alipay = Service::alipay($config)->wap($orderData);
        }else{
            $alipay = Service::alipay($config)->web($orderData);
        }
        return $alipay;
    }

    /**
     * 发起微信支付请求
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @param $config
     * @return \Yansongda\Supports\Collection
     */
    private function makeWxOrder($order,$config){

        //构建支付配置
        $payload = config('pay.wxpay');
        $payload['app_id'] = $config['appid'];
        $payload['miniapp_id'] = $config['appid'];
        $payload['mch_id'] = $config['mchid'];
        $payload['key'] = $config['key'];

        $orderData = [
            'out_trade_no'  => $order['trade_no'],      //平台支付单号trade_no   商户订单 out_trade_no
            'total_fee'     => (float) $order['amount'] *  '100.00',   //支付金额
            'body'          => $order['subject'], //支付项目
        ];
        $wxOrder = Service::wechat($payload)->scan($orderData);
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
            Log::error($wxOrder);
            Log::error('获取预支付订单失败');
        }
        return $wxOrder;
    }

    /**
     * 发起QQ钱包支付请求
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @param $config
     * @return mixed
     */
    private function makeQpayOrder($order,$config)
    {
        //入参
        $orderData = [
            'out_trade_no'  => $order['trade_no'],      //平台支付单号
            'total_fee'     => $order['amount']*100,   //支付金额
            'body'          => $order['subject'], //支付项目
        ];
        $qpay = Service::qpay(config('qq.qpay'))->scan($orderData);
        if ($qpay['return_code'] != 'SUCCESS' || $qpay['result_code'] != 'SUCCESS') {
            Log::error(json_encode($qpay));
            Log::error('获取预支付订单失败');
        }
        return $qpay;
    }

}