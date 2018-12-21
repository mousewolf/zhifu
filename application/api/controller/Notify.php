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
    public function person($channel = 'wxpay'){

        $apiurl = $this->request->request("apiurl");
        $sign = $this->request->request("sign");

        //验证签名
        if ($sign != md5(md5($apiurl))) {
            $this->result("签名密钥不正确");
        }
        $this->result("配置成功");
        echo $channel;
    }

    /**
     * 同步回调 【不做数据处理 获取商户回调地址返回就行了】
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $channel
     *
     */
    public function callback($channel = 'wxpay'){
        //1.拿out_trade_no
        //2.查订单获取  商户return_url
        //3.redirect 带参数跳转
        $this->redirect('');
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

             //支付分发
            $result = ApiPayment::$channel()->notify();

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
}