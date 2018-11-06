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

use app\common\model\BalanceSettle as SettleModel;
use think\Db;
use think\Log;

class BalanceSettle extends BaseLogic
{

    /**
     * 获取订单结算列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param string $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getOrderSettleList($where = [], $field = 'a.*,b.account as myaccount', $order = 'a.create_time desc', $paginate = 15)
    {
        $this->modelBalanceSettle->limit = !$paginate;
        $this->modelBalanceSettle->alias('a');

        $join = [
            ['user_account b', ' b.uid = a.uid'],
        ];

        $this->modelBalanceSettle->join = $join;
        return $this->modelBalanceSettle->getList($where, $field, $order, $paginate);
    }

    /**
     * 结算列表总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @return mixed
     */
    public function getOrderSettleCount($where = []){
        return $this->modelBalanceSettle->getCount($where);
    }

    /**
     * 每日一单结算
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $dataArr
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function settleBalanceToCash($dataArr){

        $dataArr['settle_no'] = create_order_no();
        $dataArr['remarks'] = date('Ymdhis') . "自动结算";

        //每日一单结算
        $resArr = $this->where(['uid'=>$dataArr['uid']])->whereTime('create_time','d')->find();
        //已有订单  (TODO进行更新)
        if (!is_null($resArr)) {
            Db::startTrans();
            try {
                //增加余额  （单独写）
                Log::notice("资金变动 enable结算至 balance");
                (new \app\common\model\Balance())->setIncOrDec(['uid'=>$dataArr['uid']],'setInc','balance',$dataArr['amount']);

                //生成结算记录
                Log::notice("生成日结算记录");
                (new SettleModel())->save($dataArr);

                //记录资金变动 --扣减enable
                Log::notice("记录资金变动 --扣减enable");
                (new BalanceChange())->creatBalanceChange($dataArr['uid'],$dataArr['amount'],$remarks = '记录资金变动 --扣减enable至balance',$enable = true,$setDec = true);

                //存入自动打款队列 后续处理
                Log::notice("存入日打款队列");
                (new Queue())->pushJobDataToQueue("AutoSettlePaid", $dataArr, "AutoSettlePaid");

                Db::commit();


            } catch (\Exception $e) {
                Db::rollback();
                Log::error('Auto Settle Fail:[' . $e->getMessage() . ']');
            }
        }
        Log::error('Auto Settle Repeat');
    }
}