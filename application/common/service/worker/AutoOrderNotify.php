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

use app\api\service\Rest;
use app\common\library\RsaUtils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use think\Log;
use think\queue\Job;

class AutoOrderNotify
{
    /**
     * 延时
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @var int
     */
    protected static $delay = 15;

    /**
     * fire方法是消息队列默认调用的方法
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param Job $job
     * @param $data
     * @throws \GuzzleHttp\Exception\GuzzleException
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
            print("<info>The Order Job ID " . $data['id'] ." has been done and deleted"."</info>\n");
        }else{
            //通过这个方法可以检查这个任务已经重试了几次了
            if ($job->attempts() > 5) {
                print("<warn>The Order Job ID " . $data['id'] ." has been deleted and retried more than 5 times!"."</warn>\n");
                $job->delete();
            }else{
                // 也可以重新发布这个任务
                print("<info>The Order Job ID " . $data['id'] ." will be availabe again after ". (time() - $data['create_time']  + self::$delay) ." s."."</info>\n");
                $job->release(time() - $data['create_time'] + self::$delay); //$delay为延迟时间，表示该任务延迟2秒后再执行

            }

        }
    }

    /**
     * 有些消息在到达消费者时,可能已经不再需要执行了
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
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
     * @param array|mixed $data  入列数据
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function doJob($data) {

        // 根据消息中的数据进行实际的业务处理...
        if ($data['create_time'] <= time()){

            //要签名的数据
            $to_sign_data =  $this->buildResponseData($data);

            //签名头部
            $header = [
                'user-agent'    =>  "Iredcap Inc/1.0 Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.75 Safari/537.36",
                'content-type'  =>  "application/json; charset=UTF-8",
                'noncestr'      =>  Rest::createUniqid(),
                'timestamp'     =>  Rest::getMicroTime()
            ];
            //签名串
            $header['signature'] =  $this->buildSignStr($to_sign_data,$header);

            try{

                $client = new Client([
                    'headers' => $header
                ]);
                $response = $client->request(
                    'POST', $data['notify_url'],
                    [
                        //TODO 处理发送数据
                        'json' => $to_sign_data,
                    ]
                );

                if ( $response->getStatusCode() == 200){
                    // 转换对象
                    $resObj =  json_decode($response->getBody()->getContents());
                    //判断放回是否正确
                    if ($resObj->result_code == "OK" && $resObj->result_msg == "SUCCESS"){
                        //TODO 处理数据库数据（暂不处理）

                        return true;
                    }
                    return false;
                }
                return false;
            }catch (RequestException $e){
                Log::error('Notify Error:['.$e->getMessage().']');
                return false;
            }
        }
        return false;
    }

    /**
     * 构建返回数据对象
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @return string
     */
    private function buildResponseData($data){
        //除去不需要字段
        unset($data['id']);
        unset($data['uid']);
        unset($data['trade_no']);
        unset($data['status']);

        //组合参数
        $payload['result_code'] = 'OK';
        $payload['result_msg'] = 'SUCCESS';
        $payload['charge'] = $data;
        //返回string
        return json_encode($payload);
    }
    /**
     * 生成签名串
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $to_sign_data
     * @param $header
     * @return string
     */
    private function buildSignStr($to_sign_data,$header){
        $_to_sign_data = utf8_encode($header['noncestr'])
            ."\n" . utf8_encode($header['timestamp'])
            ."\n" . utf8_encode($to_sign_data);
        //生成签名并记录本次签名上下文
        $Rsa = new RsaUtils();
        //公钥生成签名
        $Rsa->setPrivateKey(CRET_PATH . 'rsa_private_key.pem');
        return $Rsa->sign($_to_sign_data);
    }

}