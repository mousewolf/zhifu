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

namespace app\index\controller;

use app\common\controller\Common;
use app\common\library\enum\CodeEnum;
use Iredcap\Pay\Charge;
use Iredcap\Pay\Pay;
use think\Cache;
use think\Log;

/**
 * Class Index
 *
 * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
 *
 */
class Index extends Common{

    // MCH ID
    const MCH_ID = '100001';
    //MCH KEY
    const MCH_KEY = '4fcc6772cea1a4b776f7d0a8f0b14252';
    //NOTIFY URL
    const NOTIFY_URL    =   'https://api.iredcap.cn/test/notify';
    //RETURN_URL
    const RETURN_URL    =   'https://api.iredcap.cn/test/return';

    /**
     * 首页
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index(){

        $this->request->get('cache') && Cache::clear();
        //文章列表
        $this->assign('article_list',$this->logicArticle->getArticleList(['status'=> 1], 'id,title,create_time'));
        return $this->fetch();
    }

    /**
     * 定价
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function pricing()
    {
        return $this->fetch();
    }

    /**
     * 帮助
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function help($id){
        $this->assign('article',$this->logicArticle->getArticleInfo(['id'=>$id]));
        //上一篇
        $map['id']=array('lt',$id);
        $this->assign('front',$this->logicArticle->getArticleInfo(['id'=>$id-1]));
        //下一篇
        $map['id']=array('gt',$id);
        $this->assign('after',$this->logicArticle->getArticleInfo(['id'=>$id+1]));
        return $this->fetch();
    }

    /**
     * 下载中心
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function download(){

        return $this->fetch();
    }

    /**
     * 新闻中心
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $id
     * @return mixed
     */
    public function news($id){

        $this->assign('article',$this->logicArticle->getArticleInfo(['id'=>$id]));
        //上一篇
        $map['id']=array('lt',$id);
        $this->assign('front',$this->logicArticle->getArticleInfo(['id'=>$id-1]));
        //下一篇
        $map['id']=array('gt',$id);
        $this->assign('after',$this->logicArticle->getArticleInfo(['id'=>$id+1]));
        return $this->fetch();
    }

    /**
     * 隐私协议
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function protocol(){
        return $this->fetch();
    }

    /**
     * 接口测试
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     * @throws \Iredcap\Pay\exception\AuthorizationException
     * @throws \Iredcap\Pay\exception\Exception
     * @throws \Iredcap\Pay\exception\InvalidRequestException
     */
    public function get(){
        $this->request->isPost() && $this->result($this->demo($this->request->post()));
        return $this->fetch();
    }

    /**
     * 支付demo
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $param
     * @throws \Iredcap\Pay\exception\AuthorizationException
     * @throws \Iredcap\Pay\exception\Exception
     * @throws \Iredcap\Pay\exception\InvalidRequestException
     * @return array
     */
    private function demo($param){
        //API 访问限制
        if (!$this->logicApi->checkFrequent('192.168.1.1')){
            return [ 'code' => CodeEnum::ERROR, 'msg' => "当日剩余请求次数为：“0”，无法继续测试。<br>更多请使用API发起支付,谢谢"];
        };
        //获取商户API
        $user = $this->logicApi->getApiInfo(['uid' => is_login()]);
        //1.设置配置参数
        Pay::setMchId($user['uid']);         // 设置 MCH ID
        Pay::setSecretKey($user['key']);  // 设置 MCH KEY
        Pay::setNotifyUrl(self::NOTIFY_URL); // 设置 NOTIFY URL
        Pay::setReturnUrl(self::RETURN_URL); // 设置 RETURN URL
        Pay::setPrivateKeyPath(CRET_PATH.'/100001/rsa_private_key.pem'); // 设置私钥
        Pay::setPublicKeyPath(CRET_PATH .'/100001/rsa_public_key.pem'); // 设置公钥
        Pay::setPayPublicKeyPath(CRET_PATH .'/rsa_public_key.pem'); // 设置平台公钥

        //2.支付主体 构建请求参数
        $payload = [
            "out_trade_no" =>  $param['order_no'],
            "subject" => $param['body'],
            "body" => $param['body'],
            "amount" => $param['sum'],
            "currency" =>'CNY',
            "channel" => strtoupper('WXSCAN'), //支付方式
            "extparam" => [
                "openid" => "ow_".getRandChar('32')
            ], //支付附加参数

        ];
        $order = [];
        //提交支付
        $order = Charge::create($payload);
//        // 自定义二维码配置
//        $config = [
//            'title'         => true,
//            'generate'      => 'writefile',
//            'title_content' => '扫码支付'
//        ];
//
//        $qr_code = new QrcodeLib($config);
//        $qr_img = $qr_code->createServer($config['title']);
//        $order['qrcode'] = $qr_img['data']['url'];
        $this->result(CodeEnum::SUCCESS,$order ?'交易成功' : '交易失败',$order);
    }

}