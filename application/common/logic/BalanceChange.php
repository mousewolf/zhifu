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

namespace app\common\logic;

use think\Db;
use think\Log;

class BalanceChange extends BaseLogic
{
    /**
     * 资金变动记录
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @module Admin
     *
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getBalanceChangeList($where = [], $field = true, $order = 'create_time desc', $paginate = 15)
    {
        $this->modelBalanceChange->limit = !$paginate;
        return $this->modelBalanceChange->getList($where, $field, $order, $paginate);
    }

    /**
     * 资金变动记录总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @return mixed
     */
    public function getBalanceChangeCount($where = []){
        return $this->modelBalanceChange->getCount($where);
    }

    /**
     * 变动记录 (默认记录 待结余额 增加)
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @module Api
     *
     * @param $uid
     * @param $amount
     * @param string $remarks
     * @param bool $enable
     * @param bool $setDec
     * @return bool
     * @throws \think\exception\DbException
     */
    public function creatBalanceChange($uid,$amount,$remarks = '未知变动记录',$enable = false,$setDec = false){

        $user = (new \app\common\model\Balance())->getUserBalance($uid);
        if(!is_null($user)){
            $data['uid'] = $uid;
            $data['type'] =  $enable ? 'enable' :'disable';
            $data['preinc'] =  $user[$data['type']];
            $data['increase'] = $setDec ?'0.000': $amount;
            $data['reduce'] = $setDec ? $amount : '0.000';
            $data['suffixred'] = $setDec ? $data['preinc'] - $amount : $data['preinc'] + $amount;
            $data['remarks'] = $remarks;
            //数据提交
            Db::startTrans();
            try{

                (new \app\common\model\BalanceChange())->setInfo($data);
                (new \app\common\model\Balance())->setIncOrDec(['uid'=>$uid],$setDec ? 'setDec' :'setInc',$enable ? 'enable' :'disable',$amount);
                Db::commit();

            }catch (\Exception $e) {
                Db::rollback();
                //记录日志
                Log::error("Creat Balance Change Error:[{$e->getMessage()}]");
            }

        }
        return false;
    }
}