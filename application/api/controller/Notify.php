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

use app\common\library\exception\ForbiddenException;
use Yansongda\Pay\Exceptions\Exception;
use Yansongda\Pay\Pay;
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
     * 统一异步通知
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $channel
     *
     * @return mixed
     * @throws ForbiddenException
     */
    public function handle($channel = 'wechat'){
        try{
            $pay = Pay::$channel(self::getOrderPayConfig($channel));

            $data = $pay->verify(); //验签

            $this->logicNotify->handle($data);

            return  $pay->success()->send();

        } catch (Exception $e) {
            Log::error('微信支付验签失败:['. $e->getMessage() .']');
            throw new ForbiddenException([
                'errcode'   => '100003',
                'msg'   => '微信支付验签失败，请检查数据'
            ]);
        }
    }


//    /**
//     * Wechat
//     *
//     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
//     *
//     * @return \Symfony\Component\HttpFoundation\Response
//     * @throws ForbiddenException
//     */
//    public function wechat()
//    {
//        try{
//            $wechat = Pay::wechat(self::getWxOrderPayConfig());
//
//            $data = $wechat->verify(); // 是的，验签就这么简单！
//
//            $this->logicNotify->handle($data);
//
//            return  $wechat->success()->send();
//
//        } catch (Exception $e) {
//            Log::error('微信支付验签失败:['. $e->getMessage() .']');
//            throw new ForbiddenException([
//                'errcode'   => '100003',
//                'msg'   => '微信支付验签失败，请检查数据'
//            ]);
//        }
//    }

//    /**
//     * Alipay
//     *
//     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
//     *
//     * @return \Symfony\Component\HttpFoundation\Response
//     * @throws ForbiddenException
//     */
//    public function alipay(){
//
//
//        try{
//            $alipay = Pay::alipay(self::getAliOrderPayConfig());
//
//            $data = $alipay->verify(); // 是的，验签就这么简单！
//
//            $this->logicNotify->handle($data);
//
//            return $alipay->success()->send();
//        } catch (Exception $e) {
//            Log::error('支付宝验签失败:['. $e->getMessage() .']');
//            throw new ForbiddenException([
//                'errcode'   => '100003',
//                'msg'   => '支付宝验签失败，请检查数据'
//            ]);
//        }
//    }

    private function getOrderPayConfig($channel){
        if (empty($channel)){
            return false;
        }
        switch ($channel){
            case 'wechat':
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
        //支付宝异步通知POST XML返回数据
        libxml_disable_entity_loader(true);
        //Object  对象
        $response = json_decode(json_encode(simplexml_load_string(file_get_contents("php://input"), 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE));
        Log::notice("WxNotify:" . json_encode($response));
        $wx_config = json_decode($this->logicOrders->getOrderPayConfig($response->out_trade_no), true);
        //配置载入
        $wx_config = array_merge(config('pay.wechat'), $wx_config);
        return  $wx_config;

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
        $ali_config = json_decode($this->logicOrders->getOrderPayConfig($response['out_trade_no']), true);
        //配置载入
        $ali_config = array_merge(config('pay.alipay'), $ali_config);
        return  $ali_config;
    }
}