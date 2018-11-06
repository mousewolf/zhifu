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


namespace app\api\logic;

use think\Db;
use think\Exception;
use think\Log;
use Yansongda\Pay\Pay;
use app\common\library\enum\OrderStatusEnum;

class Notify extends BaseApi
{

    /**
     * 支付回调助手
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @return bool
     *
     */
    public function handle($data){
        Db::startTrans();
        try{
            //获取支付订单号
            $trade_no = $data['out_trade_no'];
            //查找订单
            $order = $this->modelOrders->where(['trade_no'=>$trade_no])->lock(true)->find();
            Log::notice('查找订单');
            //判断状态
            if ($order->status == 1) {
                Log::notice('更新订单状态');
                //更新订单状态
                $this->updateOrderStatus($order->id, true);
                Log::notice('自增商户资金');
                //自增商户资金
                $this->changeBalanceValue($order->uid, $order->amount,$trade_no);
                //异步消息商户
                Log::notice('异步消息商户');
                $this->logicQueue->pushJobDataToQueue('AutoOrderNotify' , $order , 'AutoOrderNotify');

            }else{
                //更新订单状态
                $this->updateOrderStatus($order->id, false);
            }
            //提交更改
            Db::commit();
            return false;
        } catch (Exception $ex) {
            Db::rollback();
            Log::error('错误'.$ex->getMessage());
        }

        return true;
    }

    /**
     * 更新支付单状态
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $id
     * @param $success
     */
    private function updateOrderStatus($id, $success)
    {
        $this->modelOrders->changeOrderStatusValue([
            'status'  => $success ? OrderStatusEnum::PAID : OrderStatusEnum::UNPAID
        ], [
            'id'=>$id
        ]);
    }


    /**
     * 更新商户账户余额  交易前disable 交易完成enable <之间系统当日结算次日打款> 打款从balance 提取
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $uid
     * @param $fee
     * @param $trade_no
     */
    public function changeBalanceValue($uid, $fee,$trade_no)
    {
        //支付成功  扣除待支付金额 (这个操作就只有两个地方   自动关闭订单和这里)
        $this->logicBalanceChange->creatBalanceChange($uid,$fee,$trade_no . '支付成功，转移至待结算金额',false,true);
        //支付成功  写入待结算金额
        $this->logicBalanceChange->creatBalanceChange($uid,$fee,$trade_no . '支付成功，待支付金额转入',true,false);

    }
}
