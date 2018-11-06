<?php
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
        $appChannelMap = self::where(['code_id'=> $CodeId,'status'=> 1])->cache(true,'60')->column('id,action,param');
        if ($appChannelMap){
            //随机ID参数返回
            return [
                'id'=> $key = array_rand($appChannelMap),
                'action'=> $appChannelMap[$key]['action'],
                'param'=>json_decode($appChannelMap[$key]['param'],true)
            ];
        }
        return false;
    }
}