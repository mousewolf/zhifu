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

namespace app\api\service\response;

class BuildSign extends ApiSend
{

    /**
     * 数据签名
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $payload
     * @return mixed|void
     */
    public function doBuild($payload)
    {
        $_to_sign_data = utf8_encode(self::get('noncestr'))
            ."\n" . utf8_encode(self::get('timestamp'))
            ."\n" . utf8_encode(self::get('authentication'))
            ."\n" . utf8_encode(json_encode(static::get('ApiResposeData')));
        //生成签名并记录本次签名上下文
        self::set('signature',self::sign($_to_sign_data));
    }

}