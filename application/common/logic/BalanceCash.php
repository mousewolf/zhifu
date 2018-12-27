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


use app\common\library\enum\CodeEnum;
use think\Db;
use think\Log;

class BalanceCash extends BaseLogic
{

    /**
     * 获取打款列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param string $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getOrderCashList($where = [], $field = 'a.*,b.account as myaccount', $order = 'a.create_time desc', $paginate = 15)
    {
        $this->modelBalanceCash->alias('a');

        $join = [
            ['user_account u', 'a.account = u.id'],
            ['banker b', 'u.bank_id = b.id']
        ];

        $this->modelBalanceCash->join = $join;

        return $this->modelBalanceCash->getList($where, $field, $order, $paginate);
    }

    /**
     * 获取打款列表总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @return mixed
     */
    public function getOrderCashCount($where = []){
        return $this->modelBalanceCash->getCount($where);
    }

    /**
     * 新增提现申请记录
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     *
     * @return array
     */
    public function saveUserCashApply($data){
        //TODO 数据验证
        $validate = $this->validateBalance->check($data);

        if (!$validate) {
            return ['code' => CodeEnum::ERROR, 'msg' => $this->validateBalance->getError()];
        }
        //TODO 添加数据
        Db::startTrans();
        try{
            $data['cash_no'] = create_order_no();
            //提现
            $this->modelBalanceCash->setInfo($data);
            //资金变动 - 资金记录
           $this->logicBalanceChange->creatBalanceChange($data['uid'],$data['amount'],$remarks = '提现扣减可用金额', 'enable', true);

            Db::commit();

            action_log('新增', '个人提交提现申请'. $data['remarks']);

            return ['code' => CodeEnum::SUCCESS, 'msg' => '新增提现申请成功'];
        }catch (\Exception $ex){
            Log::error("新增提现申请出现错误 : " . $ex->getMessage());
            Db::rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => config('app_debug') ? $ex->getMessage()
                : '新增提现申请出现错误' ];
        }
    }
}