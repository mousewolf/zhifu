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
    public function getOrderSettleList($where = [], $field = 'a.*,b.account as myaccount', $order = 'a.create_time desc', $paginate = 10)
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
     */
    public function settleBalanceToCash($dataArr){

        $dataArr['settle_no'] = create_order_no();
        $dataArr['remarks'] = date('Ymdhis') . "自动结算";

        //做判断  仅允许一次 执行时间在23：50

        Db::startTrans();
        try {

            //生成结算记录
            (new SettleModel())->setInfo($dataArr);
            //记录资金变动
            (new BalanceChange())->creatBalanceChange($dataArr['uid'],$dataArr['amount'],'支出' . $dataArr['remarks'],'enable',true);
            (new BalanceChange())->creatBalanceChange($dataArr['uid'],$dataArr['amount'],'收入' . $dataArr['remarks'],'balance');

            //TODO 判断设置有自动打款的商户进行当日打款 --等待处理

            //存入自动打款队列 后续处理
            //Log::notice("存入打款队列");
            //(new Queue())->pushJobDataToQueue("AutoSettlePaid", $dataArr, "AutoSettlePaid");

            Db::commit();
            return;
        } catch (\Exception $e) {
            Db::rollback();
            Log::error('Auto Settle Fail:[' . $e->getMessage() . ']');
        }
    }
}