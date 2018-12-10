<?php


namespace app\api\service\payment;


use app\api\service\ApiPayment;
use app\common\library\exception\OrderException;
use think\Log;

class Official extends ApiPayment
{

    /**
     * 微信支付
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @param bool $notify
     *
     * @return array
     * @throws OrderException
     */
    public function wxpay($order, $notify = false){

        if ($notify){
            return $this->verifyWxOrderNotify();
        }
        $unified = array(
            'appid' => $this->config['app_id'],
            'attach' => 'cmpay',             //商家数据包，原样返回，如果填写中文，请注意转换为utf-8
            'body' => $order['subject'],
            'mch_id' =>  $this->config['mch_id'],
            'nonce_str' => self::createNonceStr(),
            'notify_url' => $this->config['notify_url'],
            'out_trade_no' => $order['trade_no'],
            'spbill_create_ip' => request()->ip(),
            'total_fee' => intval(bcmul(100, $order['amount'])),       //单位 转为分
            'trade_type' => 'NATIVE',
        );

        $unified['sign'] = self::getWxpaySign($unified, $this->config['key']);

        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', self::arrayToXml($unified));

        $result = self::xmlToArray($responseXml);

        if (!isset($result['return_code']) || $result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            Log::error('Create Wechat API Error:'.($result['return_msg'] ?? $result['retmsg']).'-'.($result['err_code_des'] ?? ''));
            throw new OrderException([
                'msg'   => 'Create Wechat API Error:'.($result['return_msg'] ?? $result['retmsg']).'-'.($result['err_code_des'] ?? ''),
                'errCode'   => 200009
            ]);
        }
        return [
            'prepay_id' => $result['prepay_id'],
            'order_qr' => $result['code_url']
        ];
    }

    /**
     * QQ支付
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @param bool $notify
     *
     * @return array
     * @throws OrderException
     */
    public function qqpay($order, $notify = false){
        if ($notify){
            return $this->verifyWxOrderNotify();
        }
        $unified = array(
            'appid' => $this->config['app_id'],
            'attach' => 'cmpay',             //商家数据包，原样返回，如果填写中文，请注意转换为utf-8
            'body' => $order['subject'],
            'mch_id' =>  $this->config['mch_id'],
            'nonce_str' => self::createNonceStr(),
            'notify_url' => $this->config['notify_url'],
            'out_trade_no' => $order['trade_no'],
            'spbill_create_ip' => request()->ip(),
            'total_fee' => intval(bcmul(100, $order['amount'])),       //单位 转为分
            'trade_type' => 'NATIVE',
        );

        $unified['sign'] = self::getWxpaySign($unified, $this->config['key']);

        $responseXml = self::curlPost('https://qpay.qq.com/cgi-bin/pay/qpay_unified_order.cgi', self::arrayToXml($unified));

        $result = self::xmlToArray($responseXml);

        if (!isset($result['return_code']) || $result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            Log::error('Create QQ API Error:'.($result['return_msg'] ?? $result['retmsg']).'-'.($result['err_code_des'] ?? ''));
            throw new OrderException([
                'msg'   => 'Create QQ API Error:'.($result['return_msg'] ?? $result['retmsg']).'-'.($result['err_code_des'] ?? ''),
                'errCode'   => 200009
            ]);
        }
        return [
            'prepay_id' => $result['prepay_id'],
            'order_qr' => $result['code_url']
        ];
    }

    /**
     * 支付宝支付
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     * @param bool $notify
     *
     * @return array|bool
     * @throws OrderException
     */
    public function alipay($order, $notify = false){

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
        $commonConfigs = array(
            //公共参数
            'app_id' => $this->config['app_id'],
            'method' => 'alipay.trade.precreate',             //接口名称
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
        return [
            'order_qr' => $result['qr_code']
        ];
    }

    /******************微信***********************************/

    /**
     * 回调验签
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     * @return array
     * @throws OrderException
     */
    public function verifyWxOrderNotify(){
        libxml_disable_entity_loader(true);
        //Object  对象
        $response = json_decode(json_encode(simplexml_load_string(file_get_contents("php://input"), 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE));

        if (self::getWxpaySign(obj2arr($response), $this->config['key']) !== $response->sign) {
            Log::error('Verify WxOrder Notify Error');
            throw new OrderException([
                'msg'   => 'Verify WxOrder Notify Error',
                'errCode'   => 200010
            ]);
        }
        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        return obj2arr($response);

    }

    /**
     * 获取微信签名
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $params
     * @param $key
     *
     * @return string
     */
    public static function getWxpaySign($params, $key)
    {
        ksort($params, SORT_STRING);
        $unSignParaString = self::formatWxpayQueryParaMap($params);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
        return $signStr;
    }

    /**
     * 微信字符串排序
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $paraMap
     *
     * @return bool|string
     */
    protected static function formatWxpayQueryParaMap($paraMap)
    {
        $buff = "";
        ksort($paraMap);

        foreach ($paraMap as $k => $v) {
            $buff .= ($k != 'sign' && $v != '' && !is_array($v)) ? $k.'='.$v.'&' : '';
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /******************************支付宝******************************************/

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