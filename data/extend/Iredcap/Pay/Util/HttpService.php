<?php
/**
 * +---------------------------------------------------------------------+
 * | Yubei         | [ WE CAN DO IT JUST THINK ]
 * +---------------------------------------------------------------------+
 * | Licensed    | http://www.apache.org/licenses/LICENSE-2.0 )
 * +---------------------------------------------------------------------+
 * | Author       | Brian Waring <BrianWaring98@gmail.com>
 * +---------------------------------------------------------------------+
 * | Company   | 小红帽科技      <Iredcap. Inc.>
 * +---------------------------------------------------------------------+
 * | Repository | https://github.com/BrianWaring/Yubei
 * +---------------------------------------------------------------------+
 */

namespace Iredcap\Pay\Util;

use Iredcap\Pay\Exception\Exception;
use Iredcap\Pay\Exception\InvalidResponseException;
use Iredcap\Pay\Pay;

Log::Init();
class HttpService
{
    /**
     * @var array 需要发送的头信息
     */
    private $header = [];

    /**
     * @var string 需要访问的URL地址
     */
    private $uri = '';
    /**
     * @var array 需要发送的数据
     */
    private $payload = [];
    /**
     * @var array 需要加密的数据
     */
    private $encrypt = [];

    /**
     * 设置需要发送的HTTP头信息
     *
     * @return void
     */
    private function setHeader(){

        $noncestr = Encryption::createUniqid();
        $timestamp = Encryption::getMicroTime();

        $header = [
            'Content-Type:application/json; charset='.Pay::CAHRSET,
            'restUrl:'.$this->uri,
            'noncestr:'.$noncestr,
            'timestamp:'.$timestamp,
            'authentication:'.Pay::getSecretKey(),
            'X-Yubei-Client-User-Agent:'.json_encode([
                'version:'.Pay::getApiVersion(),
                'terminal:'.php_uname(),
            ])
        ];

        $this->header = $header;
        $this->encrypt = [
            "noncestr"=>$noncestr,
            "timestamp"=>$timestamp,
        ];
    }

    /**
     * @param $data
     * @param string $sep
     * @return string
     */
    public static function makeQuery($data, $sep = '&'){
        $encoded = '';
        while (list($k,$v) = each($data)) {
            $encoded .= ($encoded ? "$sep" : "");
            $encoded .= rawurlencode($k)."=".rawurlencode($v);
        }
        return $encoded;
    }

    /**
     * 设置要发送的数据信息
     *
     * 注意：本函数只能调用一次，下次调用会覆盖上一次的设置
     *
     * @param array 设置需要发送的数据信息，一个类似于 array('name1'=>'value1', 'name2'=>'value2') 的一维数组
     * @return void
     */
    private function setVar($vars){
        if (empty($vars)) {
            return;
        }
        if (is_array($vars)){
            $this->payload = $vars;
        }
    }

    /**
     * 设置要请求的URL地址
     *
     * @param string $url 需要设置的URL地址
     * @return void
     */
    private function setUrl($url){
        if ($url != '') {
            $this->uri = $url;
        }
    }

    /**
     * 发送HTTP GET请求
     *
     * @param string $url 如果初始化对象的时候没有设置或者要设置不同的访问URL，可以传本参数
     * @param array $vars 需要单独返送的GET变量
     * @param int $timeout 连接对方服务器访问超时时间，单位为秒
     * @return mixed
     * @throws Exception
     */
    public function get($url = '', $vars = [], $timeout = 5 ){
        $this->setUrl($url);
        $this->setHeader();
        $this->setVar($vars);
        return $this->send('GET', $timeout);
    }


    /**
     *  发送HTTP POST请求
     *
     * @param string $url 如果初始化对象的时候没有设置或者要设置不同的访问URL，可以传本参数
     * @param array $vars 需要单独返送的GET变量
     * @param int $timeout 连接对方服务器访问超时时间，单位为秒
     * @return mixed
     * @throws Exception
     */
    public function post($url = '', $vars = [], $timeout = 5 ){
        $this->setUrl($url);
        $this->setHeader();
        $this->setVar($vars);
        return $this->send('POST', $timeout);
    }

    /**
     * 发送HTTP请求核心函数
     *
     * @param string $method  使用GET还是POST方式访问
     * @param int $timeout  连接对方服务器访问超时时间，单位为秒
     * @param array $options
     * @return mixed
     * @throws Exception
     */
    private function send($method = 'GET', $timeout = 30, $options = []){
        //处理参数是否为空
        if ($this->uri == ''){
            throw new Exception(__CLASS__ .": Access url is empty");
        }

        //初始化CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->uri);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //设置特殊属性
        if (!empty($options)){
            curl_setopt_array($ch , $options);
        }
        //请求方式判断
        if ($method == 'GET'){

        }else{
            curl_setopt($ch, CURLOPT_POST, 1 );
        }
        $signature = $this->to_sign_data(json_encode($this->payload));
        //数据签名
        $this->header[]='signature:'.$signature;
        //设置HTTP缺省头
        if (empty($this->header)){
            $this->setHeader();
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        //发送请求读取输数据
        $data = curl_exec($ch);

        try{
            $body_data = null;
            $res_header = substr($data, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $body_data = substr($data, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $response_code=intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
            Log::DEBUG('Response Data :'.$body_data);
            //返回数据验签
            //验签需要返回数据与返回sign比对校验
            if ($response_code<400){
               $this->to_verify_data($res_header, $body_data);
            }
        }catch (Exception $e) {
            return $e->getMessage();
        }
        curl_close($ch);
        return json_decode($body_data,true);
    }

    /**
     * 签名数据
     *
     * @param $body_data
     * @return string
     */
    private function to_sign_data($body_data=null){
        $_cur_uri = $_cur_uri_query_string = stristr(Pay::$baseUrl,'/pay/');
        $_query_string = $_query_string_index = strpos($_cur_uri_query_string,'?');
        if (!empty($_query_string_index)){
            $_cur_uri = substr($_cur_uri_query_string,0,$_query_string_index);//uri
            $_query_string = substr($_cur_uri_query_string,$_query_string_index+1);//query string
        }
        $_to_sign_data = utf8_encode($_cur_uri)
                    ."\n".utf8_encode($_query_string)
                    ."\n".utf8_encode($this->encrypt['noncestr'])
                    ."\n".utf8_encode($this->encrypt['timestamp'])
                    ."\n".utf8_encode(Pay::getSecretKey())
                    ."\n".utf8_encode($body_data);
        return Encryption::sign($_to_sign_data);
    }

    /**
     * 验签
     *
     * @param $res_header
     * @param $body_data
     * @throws InvalidResponseException
     */
    private function to_verify_data($res_header, $body_data)
    {
        $res_header_array = explode("\r\n", $res_header);

        $_res_nonce = '';
        $_res_timestamp = '';
        $_res_sign = '';

        foreach ($res_header_array as $loop) {
            if (strpos($loop, "noncestr") !== false) {
                $_res_nonce = trim(substr($loop, 9));
            } elseif (strpos($loop, "timestamp") !== false) {
                $_res_timestamp = trim(substr($loop, 11));
           } elseif (strpos($loop, "signature") !== false) {
                $_res_sign = trim(substr($loop, 10));
            }
        }

        $_to_verify_data = utf8_encode($_res_nonce)
                        ."\n".utf8_encode($_res_timestamp)
                        ."\n".utf8_encode(Pay::getSecretKey())
                        ."\n".utf8_encode(json_encode(json_decode($body_data,true)['charge']));
        $verify_result = Encryption::verify($_to_verify_data, $_res_sign);
        if(empty($verify_result) || intval($verify_result)!=1){
            throw new InvalidResponseException("Invalid Response.[Response Data And Sign Verify Failure.]");
        }

        if ((float)Encryption::getMicroTime() - (float)$_res_timestamp >= 2*60*1000){
            throw new InvalidResponseException("Invalid Response.[Response Time Is Invalid.]");
        }
    }
}