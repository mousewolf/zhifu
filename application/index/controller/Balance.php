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

namespace app\index\controller;


class Balance extends Base
{
    /**
     * 资金变动记录
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function record(){
        $where = ['uid' => is_login()];
        $this->assign('list', $this->logicBalanceChange->getBalanceChangeList($where,true, 'create_time desc', 10));

        return $this->fetch();
    }

    /**
     * 打款记录
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function paid(){
        $where = ['a.uid' => is_login()];
        $this->assign('list', $this->logicBalanceCash->getOrderCashList($where));

        return $this->fetch();
    }

    /**
     * 自主提现申请
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function apply(){
        return $this->fetch();
    }
}