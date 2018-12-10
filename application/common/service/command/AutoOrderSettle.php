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

namespace app\common\service\command;


use app\common\logic\BalanceSettle;
use app\common\logic\Orders;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Log;

class AutoOrderSettle extends Command
{

    /**
     * 配置定时器的信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    protected function configure()
    {
        $this->setName('AutoOrderSettle')->setDescription('AutoOrderSettle');
    }

    /**
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param Input $input
     * @param Output $output
     * @return int|null|void
     */
    protected function execute(Input $input, Output $output)
    {
        // 输出到日志文件
        $output->writeln("AutoOrderSettle start");
        // 定时器需要执行的内容
        try{
            //查渠道订单
            $subQuery = Db::table('cm_orders')->alias('a')
                ->join([['cm_pay_channel n','a.cnl_id = n.id'],['cm_user_profit p','n.id = p.cnl_id']])->where(['a.status'   => 2 ])->whereTime('a.create_time','d')
                ->field('a.uid,a.cnl_id,truncate(sum(a.amount),3) as amount,truncate(sum(a.amount * (n.rate +(1-p.uprice))),3) as fee,truncate(sum(a.amount * (1 - n.rate) * p.uprice),3) as actual')
                ->group('cnl_id')->buildSql();
            //查用户对应渠道费率
            //查用户订单
            $resArr =Db::table($subQuery.' a')
                ->field('a.uid,truncate(sum(a.amount),3) as amount,truncate(sum(a.fee),3) as fee,truncate(sum(a.actual),3) as actual') //,truncate(avg(a.rate),3) as rate
                ->group('a.uid')
                ->select();
            foreach ($resArr as $v){
                (new BalanceSettle())->settleBalanceToCash($v);
            }

        }catch (\Exception $e){
            Log::error("Auto Settle Fail:[".$e->getMessage()."]");
        }

        // .....
        $output->writeln("AutoOrderSettle end....");
    }

}