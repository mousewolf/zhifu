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

namespace app\api\controller;;

use think\Log;
use Yansongda\Pay\Pay;

class Notify extends BaseApi
{
    /**
     * WxPay Notify
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function wxNotify()
    {
        $wxpay = Pay::wechat($this->getWxOrderPayConfig());
        $this->logicNotify->handle($wxpay->verify());
        return $wxpay->success()->send();
    }

    /**
     * 获取此订单对应支付通道配置
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return array
     */
    private function getWxOrderPayConfig()
    {

        libxml_disable_entity_loader(true);
        //Object  对象
        $response = json_decode(json_encode(simplexml_load_string(file_get_contents("php://input"), 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE));
        Log::notice("Notify:" . json_encode($response));
        $payload = json_decode($this->logicOrders->getOrderPayConfig($response->out_trade_no), true);
        //构建支付配置
        $config = config('pay.wxpay');
        $config['app_id'] = $payload['appid'];
        $config['miniapp_id'] = $payload['appid'];
        $config['mch_id'] = $payload['mchid'];
        $config['key'] = $payload['key'];

        return $config;
    }
}