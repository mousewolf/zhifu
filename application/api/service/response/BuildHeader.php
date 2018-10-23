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

class BuildHeader extends ApiSend
{

    /**
     * 构建头信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $chargeRespose
     * @return mixed|void
     */
    public function doBuild($chargeRespose)
    {
        // 构建头信息
        $header = [
            'noncestr'  =>  self::get('noncestr'),
            'timestamp' =>  self::get('timestamp'),
            'signature' =>  self::get('signature')
        ];
        //记录本次签名
        self::set('header',$header);
    }

}