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

namespace app\common\service\worker;

use app\common\model\BalanceCash;
use think\Log;
use think\queue\Job;
use Yansongda\Pay\Pay;

class AutoSettlePaid
{
    /**
     * fire方法是消息队列默认调用的方法
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param Job $job
     * @param $data
     */
    public function fire(Job $job,$data){
        // 如有必要,可以根据业务需求和数据库中的最新数据,判断该任务是否仍有必要执行.
        $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        if(!$isJobStillNeedToBeDone){
            $job->delete();
            return;
        }
        //处理队列
        $isJobDone = $this->doJob($data);

        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            print("<info>The Paid Job ID ". md5(json_encode($data))." has been done and deleted"."</info>\n");
        }else{
            //通过这个方法可以检查这个任务已经重试了几次了
            if ($job->attempts() > 5) {
                print("<warn>The Paid Job ID ". md5(json_encode($data))." has been deleted and retried more than 5 times!"."</warn>\n");
                $job->delete();
            }else{
                // 也可以重新发布这个任务
                print("<info>The Paid Job ID ". md5(json_encode($data))." will be availabe again after </info>\n");
                $job->release(30); //$delay为延迟时间，表示该任务延迟30秒后再执行

            }}
    }

    /**
     * 有些消息在到达消费者时,可能已经不再需要执行了
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data array
     * @return bool
     */
    private function checkDatabaseToSeeIfJobNeedToBeDone($data){

        return true;
    }

    /**
     * 根据消息中的数据进行实际的业务处理
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @return bool
     */
    private function doJob($data) {
        //Need Paid Data:{"uid":100001,"amount":"99.50","fee":"1.99","actual":"97.51","rate":"0.020","settle_no":"A20181017164938A17820","remarks":"20181017044938\u81ea\u52a8\u7ed3\u7b97"}
        Log::notice('Need Paid Data:'.json_encode($data));
        try{
            //获取商户收款账户
            $data['account'] = '702154416@qq.com';

             //打款成功 存数据库 并更新结算状态信息
            $saveArr = [
                'uid'       => $data['uid'],
                'cash_no'   => create_general_no(),
                'settle_no'   => $data['settle_no'],
                'account'   => $data['account'],
                'amount'    => (float)$data['actual'],
                'remarks'   =>  $data['remarks']
            ];
            Log::notice("Save Paid Resutl :".json_encode($saveArr));

//            //读取打款账户
//            $alipay = Pay::alipay(config('paid.alipay'));
//            $result = $alipay->transfer([
//                'out_biz_no' => $saveArr['cash_no'],
//                'payee_type' => 'ALIPAY_LOGONID',
//                'payee_account' => $saveArr['account'],  //要查到商户默认提现账户
//                'amount' => '0.1',
//                'remark' => date('Ydmhis') . '平台自动打款'
//            ]);
//            Log::notice("Paid Resutl :".json_encode($result));
            //打款成功 存数据库 并更新结算状态信息
            //$result && (new BalanceCash())->allowField(true)->save($saveArr);
            return true;
        }catch (\Exception $e){
            Log::error('Auto Paid Faid:'.$e->getMessage());
            return false;
        }
        return true;
    }
}