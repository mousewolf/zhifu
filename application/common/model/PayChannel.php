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

/**
 * +---------------------------------------------------------------------+
 * | Yubei         | [ WE CAN DO IT JUST THINK ]
 * +---------------------------------------------------------------------+
 * | Licensed    | http://www.apache.org/licenses/LICENSE-2.0 )
 * +---------------------------------------------------------------------+
 * | Author       | Brian Waring <BrianWaring98@gmail.com>
 * +---------------------------------------------------------------------+
 * | Company   | 小红帽科技      <Iredcap. Inc.>
 * +---------------------------------------------------------------------+
 * | Repository | https://github.com/BrianWaring/Yubei
 * +---------------------------------------------------------------------+
 */

namespace app\common\model;


class PayChannel extends BaseModel
{

    /**
     * 依据支付方式ID获取改支付方式的所有支持渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $CodeId
     * @return array|bool
     */
    public function getChannelMap($CodeId){
        $appChannelMap = self::where(['id' => ['in',$CodeId],'status' => 1])->column('id,action,param');
        if ($appChannelMap){
            //随机ID参数返回
            return [
                'id'=> $key = array_rand($appChannelMap),
                'action'=> $appChannelMap[$key]['action'],
                'param'=>json_decode($appChannelMap[$key]['param'],true)
            ];
        }
        return $this->getChannelMap($CodeId);
    }
}