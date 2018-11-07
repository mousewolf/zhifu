<?php
/**
 * +----------------------------------------------------------------------
 *   | 草帽支付系统 [ WE CAN DO IT JUST THINK ]
 * +----------------------------------------------------------------------
 *   | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 * +----------------------------------------------------------------------
 *   | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 *   | Author: Brian Waring <BrianWaring98@gmail.com>
 * +----------------------------------------------------------------------
 */

namespace app\api\service\payment;

use app\api\service\ApiPayment;
use app\common\library\exception\ParameterException;
use think\Log;

class QqScan extends ApiPayment
{
    /**
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @return array
     */
    public function pay($order){

        $wxOrder = self::unifiedorder($order['amount'],$order['trade_no'],$order['subject']);
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
            Log::error($wxOrder);
            Log::error('获取预支付订单失败');
        }
        return $wxOrder;
    }

    /**
     * 异步通知
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     * @throws ParameterException
     */
    public function notify()
    {
        $retArr = self::xmlToArray(file_get_contents('php://input'));
        if (self::getSign($retArr, $this->config['key']) !== $retArr['sign']) {
            throw new ParameterException([
                'msg'   => '签名错误'
            ]);
        }
        return $retArr;
    }

    /**
     * 同一预下单
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $totalFee
     * @param $outTradeNo
     * @param $orderBody
     * @return array
     */
    private function unifiedorder($totalFee, $outTradeNo, $orderBody)
    {

        $unified = array(
            'appid' => $this->config['appid'],
            'attach' => 'pay',   //商家数据包，原样返回，如果填写中文，请注意转换为utf-8
            'body' => $orderBody,
            'mch_id' => $this->config['mchid'],
            'nonce_str' => self::createNonceStr(),
            'notify_url' => $this->config['notify_url'],
            'out_trade_no' => $outTradeNo,
            'spbill_create_ip' => request()->ip(),
            'total_fee' => intval($totalFee * 100),       //单位 转为分
            'trade_type' => 'NATIVE',
        );
        $unified['sign'] = self::getSign($unified, $this->config['key']);
        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', self::arrayToXml($unified));
        return self::xmlToArray($responseXml);
    }

    /**
     * 向微信服务器返回成功
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function success(){
        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }

    /**
     * 获取签名
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $params
     * @param $key
     * @return string
     */
    public static function getSign($params, $key)
    {
        ksort($params, SORT_STRING);
        $unSignParaString = self::formatQueryParaMap($params, false);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
        return $signStr;
    }


    protected static function formatQueryParaMap($paraMap, $urlEncode = false)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if (null != $v && "null" != $v) {
                if ($urlEncode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
}