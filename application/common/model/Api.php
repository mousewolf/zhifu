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

class Api extends BaseModel
{
    /**
     * 获取所有Key
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return array|mixed
     */
    public function appKeyMap(){
        return self::cache('appKeyMap','120')->column('id,key');
    }

}