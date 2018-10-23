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

class Orders extends BaseAdmin
{

    /**
     * 订单列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index(){

        return $this->fetch();
    }

    /**
     * 交易列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getList(){
        $where = [];
        $data = [];

        //组合搜索
        !empty($this->request->param('trade_no')) && $where['trade_no']
            = ['like', '%'.$this->request->param('trade_no').'%'];

        !empty($this->request->param('out_trade_no')) && $where['out_trade_no']
            = ['like', '%'.$this->request->param('out_trade_no').'%'];

        !empty($this->request->param('channel')) && $where['channel']
            = ['eq', $this->request->param('channel')];

        //状态
        $where['status'] = ['eq', $this->request->get('status','1')];

        //时间搜索  时间戳搜素
        !empty($this->request->param('end')) && !empty($this->request->param('start'))
        && $where['create_time'] = [
            'between', [
                strtotime($this->request->param('start',date('Y-m-d'))),
                strtotime($this->request->param('end',date('Y-m-d h:i:s')))
            ]
        ];

        !empty($this->request->param('end')) && !empty($this->request->param('start'))
        && $data = $this->logicOrders->getOrderList($where,true, 'create_time desc',false);

        $this->result($data || !empty($data) ? [CodeEnum::SUCCESS,'',$data] : [CodeEnum::ERROR,'暂无数据','']);
    }

    /**
     * 获取详情
     * 1.基本
     * 2.回调
     * 3.商户
     * 4.结算
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function details(){
        //1.基本
        $this->assign('order', $this->logicOrders->getOrderList(['id' =>$this->request->param('id')]));

        return $this->fetch();
    }


    /**
     * 退款列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function refund(){
        $where = [];
        //组合搜索
        !empty($this->request->param('keywords')) && $where['trade_no|out_trade_no|uid|id']
            = ['like', '%'.$this->request->param('keywords').'%'];

        !empty($this->request->param('channel')) && $where['channel']
            = ['eq', $this->request->param('channel')];

        !empty($this->request->param('status')) && $where['status']
            = ['eq', $this->request->param('status')];

        $this->assign('list', $this->logicOrders->getOrderList($where));

        return $this->fetch();
    }


    /**
     * 商户订单统计
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function user(){
        return $this->fetch();
    }

    /**
     * 商户交易统计
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function userList(){
        $where = [];
        //组合搜索
        !empty($this->request->param('uid')) && $where['uid']
            = ['eq', $this->request->param('uid')];
        $data = $this->logicOrders->getOrderUserStat($where);

        $this->result($data ? [CodeEnum::SUCCESS,'',$data] : [CodeEnum::ERROR,'暂无数据','']);
    }

    /**
     * 商户订单统计
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function channel(){
        return $this->fetch();
    }

    /**
     * 商户交易统计
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function channelList(){
        $where = [];
        //组合搜索
        !empty($this->request->param('cnl_id')) && $where['cnl_id']
            = ['eq', $this->request->param('cnl_id')];

        $data = $this->logicOrders->getOrderChannelStat($where);

        $this->result($data ? [CodeEnum::SUCCESS,'',$data] : [CodeEnum::ERROR,'暂无数据','']);
    }
}
