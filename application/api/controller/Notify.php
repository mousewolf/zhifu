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

use app\api\service\ApiPayment;
use think\Log;

class Notify extends BaseApi
{
    /**
     * wxScan Notify
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @throws \Exception
     */
    public function wxScan()
    {
        //TODO  分发支付网关
        $wxScan = ApiPayment::wx_scan(self::getWxOrderPayConfig());
        $this->logicNotify->handle($wxScan->notify());
        return $wxScan->success();
    }

    /**
     * qqScan Notify
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @throws \Exception
     */
    public function qqScan()
    {
        //TODO  分发支付网关
        $wxScan = ApiPayment::qq_scan(self::getWxOrderPayConfig());
        $this->logicNotify->handle($wxScan->notify());
        return $wxScan->success();
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
        return  json_decode($this->logicOrders->getOrderPayConfig($response->out_trade_no), true);

    }
}