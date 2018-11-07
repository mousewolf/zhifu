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

namespace app\admin\controller;


use app\common\library\enum\CodeEnum;

class Balance extends BaseAdmin
{
    /**
     * 资产
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index(){
        return $this->fetch();
    }

    /**
     * 商户资产列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getList(){
        $where = [];

        //组合搜索
        !empty($this->request->param('uid')) && $where['uid']
            = ['eq', $this->request->param('uid')];

        !empty($this->request->param('username')) && $where['username']
            = ['like', '%'.$this->request->param('username').'%'];

        //时间搜索  时间戳搜素
        $where['create_time'] = $this->parseRequestDate();

        $data = $this->logicBalance->getBalanceList($where, '*', 'create_time desc', false);

        $count = $this->logicBalance->getBalanceCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]
        );
    }

    /**
     * 商户账户收支明细信息（仅做记录）
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function details(){
        $this->assign('uid',$this->request->param('id'));
        return $this->fetch();
    }

    /**
     * 获取商户账户收支明细信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getDetails(){
        $where = [];

        //组合搜索
        $where['uid'] = ['eq', $this->request->param('uid')];

        //时间搜索  时间戳搜素
        $where['create_time'] = $this->parseRequestDate();

        $data = $this->logicBalanceChange->getBalanceChangeList($where, true, 'create_time desc', false);

        $count = $this->logicBalanceChange->getBalanceChangeCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]
        );

    }

    /**
     * 结算记录
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function settle(){
        return $this->fetch();
    }

    /**
     * 获取结算申请记录API
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function settleList(){
        $where = [];

        //组合搜索
        !empty($this->request->param('id')) && $where['a.id|a.uid']
            = ['like', '%'.$this->request->param('id').'%'];

        !empty($this->request->param('cash_no')) && $where['a.cash_no']
            = ['like', '%'.$this->request->param('a.cash_no').'%'];

        $data = $this->logicBalanceSettle->getOrderSettleList($where, 'a.*,b.account as myaccount', 'a.create_time desc', false);

        $count = $this->logicBalanceSettle->getOrderSettleCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]
        );

    }

    /**
     * 打款记录(仅作记录)
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function paid(){
        return $this->fetch();
    }

    /**
     * 获取打款记录API
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function paidList(){
        $where = [];
        $data = [];
        //组合搜索
        !empty($this->request->param('id')) && $where['a.id|a.uid']
            = ['like', '%'.$this->request->param('id').'%'];

        !empty($this->request->param('cash_no')) && $where['a.cash_no']
            = ['like', '%'.$this->request->param('a.cash_no').'%'];

        !empty($this->request->param('end')) && !empty($this->request->param('start'))
        && $data = $this->logicBalanceCash->getOrderCashList($where, 'a.*,b.account as myaccount', 'a.create_time desc', false);


        $count = $this->logicBalanceCash->getOrderCashCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]
        );
    }
}