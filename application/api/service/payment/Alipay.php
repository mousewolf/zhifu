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

namespace app\api\service\payment;


use app\api\service\ApiPayment;
use app\common\library\exception\OrderException;
use think\Log;

class Alipay extends ApiPayment
{
    /**
     * 支付宝扫码支付
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @param bool $notify
     *
     * @return array|bool
     * @throws OrderException
     */
    public function ali_qr($order, $notify = false){

        if ($notify){
            return $this->verifyAliOrderNotify();
        }

        //请求参数
        $requestConfigs = array(
            'out_trade_no'=> $order['trade_no'],
            'total_amount'=> sprintf("%.2f", $order['amount']), //支付宝交易范围  [0.01,100000000]
            'subject'=> $order['subject'],  //订单标题
            'timeout_express'=>'10m'       //该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
        );

        $result = self::getGenerateAlipayOrder($requestConfigs, 'alipay.trade.precreate');

        return [
            'order_qr' => $result['qr_code']
        ];
    }

    /******************************支付宝******************************************/

    /**
     * 支付宝统一
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $requestConfigs
     * @param string $trade_type
     *
     * @return mixed
     * @throws OrderException
     */
    private function getGenerateAlipayOrder($requestConfigs, $trade_type = 'alipay.trade.pay'){

        $commonConfigs = array(
            //公共参数
            'app_id' => $this->config['app_id'],
            'method' => $trade_type,             //接口名称
            'format' => 'JSON',
            'charset'=> 'utf8',
            'sign_type'=>'RSA2',
            'timestamp'=> date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'notify_url' => $this->config['notify_url'],
            'biz_content'=>json_encode($requestConfigs),
        );

        $commonConfigs["sign"] = $this->generateAlipaySign($commonConfigs, $commonConfigs['sign_type']);

        $response = $this->curlPost('https://openapi.alipay.com/gateway.do',$commonConfigs);

        $response = json_decode($response,true);

        $result = $response['alipay_trade_precreate_response'];

        if (!isset($result['code']) || $result['code'] != '10000') {
            Log::error('Create Alipay API Error:'. $result['msg'].' : '.$result['sub_msg']);
            throw new OrderException([
                'msg'   => 'Create Alipay API Error:'. $result['msg'].' : '.$result['sub_msg'],
                'errCode'   => 200009
            ]);
        }
        //数据返回
        return $result;
    }
    /**
     * 回调验签
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     * @return array
     * @throws OrderException
     */
    public function verifyAliOrderNotify(){
        $response = convertUrlArray(file_get_contents('php://input')); //支付宝异步通知POST返回数据
        $result = $this->verify($this->getSignContent($response), $response['sign'], $response['sign_type']);
        if (!$result) {
            Log::error('Verify AliOrder Notify Error');
            throw new OrderException([
                'msg'   => 'Verify AliOrder Notify Error',
                'errCode'   => 200010
            ]);
        }
        echo 'success';
        return $response;
    }

    /**
     * 支付宝签名
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $params
     * @param $signType
     *
     * @return string
     */
    protected function generateAlipaySign($params, $signType){

        return $this->sign($this->getSignContent($params), $signType);
    }

    /**
     * 签名
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @param string $signType
     *
     * @return string
     */
    protected function sign($data, $signType = "RSA") {
        $priKey = $this->config['private_key'];

        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256); //OPENSSL_ALGO_SHA256是php5.4.8以上版本才支持
        } else {
            openssl_sign($data, $sign, $res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 验证
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @param $sign
     * @param string $signType
     *
     * @return bool
     */
    protected function verify($data, $sign, $signType = 'RSA') {
        $pubKey= $this->config['public_key'];

        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }

        return $result;
    }
    /**
     * 校验$value是否非空
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $value
     *
     * @return bool
     */
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }

    /**
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $params
     *
     * @return string
     */
    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, 'utf8');
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        return $stringToBeSigned;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @param $fileType = 'utf8'
     * @return string
     */
    function characet($data, $targetCharset, $fileType = 'utf8') {
        if (!empty($data)) {
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }

}