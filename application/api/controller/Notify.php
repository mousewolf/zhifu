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
use app\common\controller\BaseApi;
use app\common\library\exception\ForbiddenException;
use app\common\library\exception\OrderException;
use think\Log;

class Notify extends BaseApi
{

    /**
     * 个人收款配置 【等待开发】
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function person(){


    }

    /**
     * 同步回调
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $channel
     *
     */
    public function callback($channel = 'wxpay'){

    }

    /**
     * 统一异步通知
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $channel
     *
     * @return mixed
     * @throws ForbiddenException
     */
    public function notify($channel = 'wxpay'){


        try{
            //配置
            $configMap = self::getOrderPayConfig($channel);

            list(,$action,$config) = array_values($configMap);

            //配置载入
            $appConfig = !empty(config('pay.' . $channel))
                ? array_merge(config('pay.' . $channel), $config)
                : $config;

             //支付分发
            $result = ApiPayment::$action($appConfig)->$channel('',true);

            $this->logicNotify->handle($result);

            return $result;
        } catch (OrderException $e) {
            Log::error('支付验签失败:['. $e->getMessage() .']');
            throw new ForbiddenException([
                'errcode'   => '100003',
                'msg'   => '支付验签失败，请检查数据'
            ]);
        }
    }

    /**
     * 配置获取
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $channel
     *
     * @return array|bool|mixed
     */
    private function getOrderPayConfig($channel){
        if (empty($channel)){
            return false;
        }
        switch ($channel){
            case 'wxpay':
            case 'qqpay':
                $config = self::getWxPayConfig();
                break;
            case 'alipay':
                $config = self::getAliPayConfig();
                break;
        }
        return $config;
    }
    /*********其他请尝试自己对接  -- 推荐写在app\api\service\payment下面  继承父类ApiPayment*********/

    /**
     * 获取微信订单对应支付通道配置
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return array
     */
    private function getWxPayConfig()
    {

        libxml_disable_entity_loader(true);
        //Object  对象
        $response = json_decode(json_encode(simplexml_load_string(file_get_contents("php://input"), 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE),true);
        Log::notice("WxNotify:" . json_encode($response));
        //获取配置
        return  self::getConfig($response);

    }

    /**
     * 获取支付宝订单对应支付通道配置
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    private function getAliPayConfig()
    {
        $response = convertUrlArray(file_get_contents('php://input')); //支付宝异步通知POST返回数据
        Log::notice("AliNotify:" . json_encode($response));

        return  self::getConfig($response);
    }

    /**
     * 依据订单获取配置
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $response
     *
     * @return mixed
     */
    private function getConfig($response = []){
        //获取配置
        $wx_config =$this->logicOrders->getOrderPayConfig($response['out_trade_no']);

        $config =  reset($wx_config);
        //配置载入
        $config['param'] = json_decode($config['param'], true);

        return $config;
    }
}