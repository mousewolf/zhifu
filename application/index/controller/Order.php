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

use app\common\library\enum\OrderStatusEnum;

class Order extends Base
{
    /**
     * 商户订单
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index(){
        $where = ['uid' => is_login()];
        //组合搜索
        !empty($this->request->get('keywords')) && $where['out_trade_no|id']
            = ['like', '%'.$this->request->get('keywords').'%'];

        !empty($this->request->get('channel')) && $where['channel']
            = ['eq', $this->request->get('channel')];
        //状态
        $where['status'] = ['eq', $this->request->get('status',OrderStatusEnum::UNPAID)];

        $this->assign('list', $this->logicOrders->getOrderList($where,'id,out_trade_no,subject,body,amount,channel,create_time,status', 'create_time desc', 15));

        return $this->fetch();
    }

    /**
     * 结算记录
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function settle(){
        $where = ['a.uid' => is_login()];
        $this->assign('list', $this->logicBalanceSettle->getOrderSettleList($where));

        return $this->fetch();
    }


}