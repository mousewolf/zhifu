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


class PayCode extends BaseModel
{

    /**
     * 依据支付名称返回ID
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $code
     * @return mixed
     */
    public function getCodeId($code){
        return self::where(['code'=> $code])->value('id');
    }
}