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

namespace app\common\model;


class Channel extends BaseModel
{
    /**
     * 获取支付渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $Cnel
     * @return mixed
     */
    public function getChannelMap($Cnel){
        $appChannelMap = self::where(['name'=> $Cnel,'status'=> 1])->cache(true,'60')->column('id,param');
        if ($appChannelMap){
            //随机ID参数返回
            return [
                'id'=>$key = array_rand($appChannelMap),
                'param'=>json_decode($appChannelMap[$key],true)
            ];
        }
        return false;
    }
}