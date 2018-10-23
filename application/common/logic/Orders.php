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


use app\common\library\exception\OrderException;
use think\Db;
use think\Log;

class Orders extends BaseLogic
{

    /**
     *
     * 获取订单列表
     *
     * @author 勇敢的小笨羊
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getOrderList($where = [], $field = true, $order = 'create_time desc', $paginate = 15)
    {
        $this->modelOrders->limit = !$paginate;
        return $this->modelOrders->getList($where, $field, $order, $paginate);
    }

    /**
     *
     * 获取结算订单列表
     *
     * @author 勇敢的小笨羊
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getOrderSettleList($where = [], $field = true, $order = 'create_time desc', $paginate = 15)
    {
        return $this->modelBalanceSettle->getList($where, $field, $order, $paginate);
    }


    /**
     * 获取控制台统计
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return array
     */
    public function getWelcomeStat(){
        $order = 'create_time desc';
        return [
            'order' => $this->modelOrders->getInfo([],"count(id) as total_orders,sum(amount) as total_fees", $order, $paginate = false),
            'user'   => $this->modelUser->getInfo([],"count(uid) as total_users", $order, $paginate = false),
            'paid' => $this->modelOrders->getInfo(['status' => 2],'count(id) as total_paid', $order, $paginate = false),
            'unpaid' => $this->modelOrders->getInfo(['status' => 1],'count(id) as total_unpaid', $order, $paginate = false),
            'uncash' => $this->modelBalanceCash->getInfo(['status' => 0],'count(id) as total_uncash', $order, $paginate = false),
            'unuser'   => $this->modelUser->getInfo(['is_verify' => 0],"count(uid) as total_unuser", $order, $paginate = false)
        ];
    }

    /**
     * 年月订单以及交易额统计
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getOrdersMonthStat(){
        $this->modelOrders->group = 'month';
        return $this->modelOrders->getList([],"count(id) as total_orders,sum(`amount`) as total_amount,FROM_UNIXTIME(create_time,'%m') as month",false,false);
    }

    /**
     * 获取商户订单统计
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param string $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getOrderUserStat($where = [],$field = "uid,sum(amount) as total_fee,count(uid) as total_orders",$order = 'create_time desc', $paginate = 15){
        $this->modelOrders->group = 'uid';
        return $this->modelOrders->getList($where,$field, $order, $paginate = false);
    }

    /**
     * 获取渠道订单统计
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param string $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getOrderChannelStat($where = [],$field = "a.cnl_id,count(a.cnl_id) as total_orders,sum(a.amount) as total_fee,b.id,b.name,b.remark,b.daily,b.rate",$order = 'a.create_time desc', $paginate = 15){
        $this->modelOrders->group = 'a.cnl_id';
        $this->modelOrders->alias('a');

        $join = [
            ['channel b', ' b.id = a.cnl_id'],
        ];

        $this->modelOrders->join = $join;
        return $this->modelOrders->getList($where,$field, $order, $paginate = false);
    }

    /**
     * 获取某订单支付通道配置ID
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order_no
     * @return mixed
     */
    public function getOrderPayConfig($order_no){

        return $this->logicChannel->getChannelParam(
            $this->modelOrders->getValue(
                ['trade_no'=>$order_no],
                'cnl_id'
            )
        )[1];
    }

    /**
     * 创建支付订单
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $orderData
     * @return mixed
     * @throws OrderException
     */
    public function createPayOrder($orderData){
        //TODO 事务处理
        Db::startTrans();
        try{
            //这里就这样   不改了
            $order = new Orders();
            $order->uid         = $orderData['mchid']; //商户ID
            $order->subject     = $orderData['subject'];//支付项目
            $order->body        = $orderData['body'];//支付具体内容
            $order->trade_no    = create_order_no();//支付单号
            $order->out_trade_no= $orderData['out_trade_no'];//商户单号
            $order->amount      = $orderData['amount'];//支付金额
            $order->currency    = $orderData['currency'];//支付货币
            $order->channel     = $orderData['channel'];//支付渠道
            $order->client_ip   = $orderData['client_ip'];//订单创建IP
            $order->return_url  = $orderData['return_url'];//通知Url
            $order->notify_url  = $orderData['notify_url'];//通知Url
            $order->extra       = json_encode(!empty($orderData['extparam']) ?$orderData['extparam']:[]);//拓展参数
            $order->save();
           //提交支付
            $result = $this->logicPay->pay($order->trade_no);  //支付
            Db::commit();
            //  余额 = 可用余额（可提现金额） + 冻结余额（待结算金额） =》 未支付金额每日清算
            //   可用余额是从冻结余额转入的
            //写入待支付金额 creatBalanceChange($uid,$amount,$remarks = '未知变动记录',$enable = false,$setDec = false)
            $result && $this->logicBalanceChange->creatBalanceChange($order->uid,$order->amount,'商户号'.$orderData['out_trade_no'].'预下单支付金额');

            return $result;

        }catch (\Exception $e){
            //记录日志
            Log::error("Create Order Error:[{$e->getMessage()}]");
            Db::rollback();
            //抛出错误异常
            throw new OrderException([
                'msg'   =>  "Create Order Error, Please Try Again Later.",
                'errCode'=> '200001'
            ]);
        }
    }

    /**
     * 设置某个字段参数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param string $value
     */
    public function setValue($where = [],$value = ''){
        $this->modelOrders->setFieldValue($where, 'cnl_id', $value);
    }
}