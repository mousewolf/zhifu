<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-11
 * Time: 下午3:16
 */
function post_data($server_url,$requestData){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $server_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //普通数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestData));
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}


function get_html($url,$url_refer,$cookie,$post_data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $url_refer);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    $content = curl_exec($ch);
    return $content;
}
function get_data_byjson($json){
    $mainOrders = $json->mainOrders;
    $post = '';
    for($i =0;$i<count($mainOrders);$i++){
        $post_str = '';
        $mainOrder = $mainOrders[$i];
        $order_num = $mainOrder->id;
        $order_num = '376461794668044623';
        $post_str = $order_num;
        $price = $mainOrder->payInfo->actualFee;
        $post_str = $post_str.'_____'.$price;
        $username = $mainOrder->buyer->nick;
        $outstr = iconv('GBK','UTF-8',$username);
        $post_str = $post_str.'_____'.$username;
        if($i==0){
            $post=$post_str;
        }else{
            $post=$post.",&,&".$post_str;
        }
    }
    return $post;
}



if(isset($argv[0])){
    if(!isset($argv[1])){
        echo "no argvs,you can use PAYED or NOPAYED exit;"."\n";exit;
    }
    if($argv[1]!='NOPAYED' && $argv[1]!='PAYED' &&$argv[1]!='SUCCESS'){
        echo "argvs erro, you can use PAYED or NOPAYED exit"."\n";exit;
    }
}else{
    $argv[1]='PAYED';
}

$url = "https://trade.taobao.com/trade/itemlist/asyncSold.htm?event_submit_do_query=1&_input_charset=utf8";
$url_refer = "https://trade.taobao.com/trade/itemlist/list_sold_items.htm?action=itemlist/SoldHisQueryAction&event_submit_do_query=1";
$cookie = "t=12d9e33956fc09b4f0cef790ecaebf73; cna=QA4JFUzoV1MCAS04mV1kiNPc; UM_distinctid=1695c9e8c192a0-094fb7e927584e-3e76035c-1fa400-1695c9e8c1a54a; tracknick=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; lgc=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; tg=0; x=e%3D1%26p%3D*%26s%3D0%26c%3D0%26f%3D0%26g%3D0%26t%3D0%26__ll%3D-1%26_ato%3D0; thw=us; _fbp=fb.1.1552354991190.455027382; hng=SG%7Czh-CN%7CSGD%7C702; l=AiQkkP/L0Nv0IYEWuqa82AKMdCkWuEgn; _m_h5_tk=bc5d14abf44208132264314a588295f4_1552448383537; _m_h5_tk_enc=be40877a2a1ded49942c92a557949e43; v=0; cookie2=12bcd26c1e2d135f04465ff8cb44e473; _tb_token_=3fe3b6460e3ee; unb=3059864953; sg=%E6%B6%A732; _l_g_=Ug%3D%3D; skt=3671c77097e85f85; cookie1=Vq9jeSVL9ZoZrM63jNovYwuOdeG2zvjie5OH%2F2CGbZ0%3D; csg=df2fc0e3; uc3=vt3=F8dByErQZvoA%2B42gTvg%3D&id2=UNDRzi5BgNtJDw%3D%3D&nk2=txBQSND3xVQ%3D&lg2=Vq8l%2BKCLz3%2F65A%3D%3D; existShop=MTU1MjQzODY3MA%3D%3D; _cc_=VFC%2FuZ9ajQ%3D%3D; dnk=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; _nk_=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; cookie17=UNDRzi5BgNtJDw%3D%3D; mt=ci=12_1; uc1=cookie14=UoTZ5iF3Akk0bQ%3D%3D&lng=zh_CN&cookie16=URm48syIJ1yk0MX2J7mAAEhTuw%3D%3D&existShop=true&cookie21=UtASsssmfaCOMId3WBg4fw%3D%3D&tag=8&cookie15=UtASsssmOIJ0bQ%3D%3D&pas=0; isg=BFJSAL6kWmXtdKaWLFRznqXCoxE-F1bPBaoeshyrJIXcL_IpBfJnDZkOn8u2X86V";

$post_data['auctionType']= 0;
$post_data['close']= 0;
$post_data['pageNum']= 1;
$post_data['pageSize']= 15;
$post_data['queryMore']= true;
$post_data['rxAuditFlag']= 0;
$post_data['rxElectronicAllFlag']= 0;
$post_data['rxElectronicAuditFlag']= 0;
$post_data['rxHasSendFlag']= 0;
$post_data['rxOldFlag']= 0;
$post_data['rxSendFlag']= 0;
$post_data['rxSuccessflag']= 0;
$post_data['rxWaitSendflag']= 0;
$post_data['tradeTag']= 0;
$post_data['useCheckcode']= false;
$post_data['useOrderInfo']= false;
$post_data['errorCheckcode']= false;
$post_data['action']= 'itemlist/SoldQueryAction';
$post_data['buyerNick']= '';
$post_data['prePageNo']= 1;
$post_data['dateBegin']= 0;
$post_data['dateEnd']= 0;
$post_data['queryOrder']= 'desc';
$post_data['lastStartRow']= '';

if($argv[1]=='NOPAYED'){
    $server_notify_url = "http://www.chncpweb.com/ownpay/notify";
    $post_data['orderStatus'] = 'NOT_PAID';
    $post_data['tabCode']= 'waitBuyerPay';
}
if($argv[1]=='PAYED'){
    $server_notify_url = "http://www.chncpweb.com/ownpay/paynotify";
    $post_data['orderStatus'] = 'PAID';
    $post_data['tabCode']= 'waitSend';
}
if($argv[1]=='SUCCESS'){
  /*  $server_notify_url = "http://www.chncpweb.com/ownpay/paynotify";
    $post_data['orderStatus'] = 'SUCCESS';
    $post_data['tabCode']= 'success';*/
}


$i = 0;
$last_time = 0;
while (1) {
    $i++;
    if($last_time == 0){
        $last_time = time();
    }else{
        echo time()-$last_time."\n";
        if(time()-$last_time<5){
          echo "sleep sss...."."\n";
           //  sleep(5-(time()-$last_time));
        }
        $last_time = time();
    }
    $html = get_html($url, $url_refer, $cookie,$post_data);

    if(empty($html)){
        echo "error maybe cookie disabled!";
    }
    $html = utf8_encode($html);
    $json = json_decode($html);

    $requestData['data'] = get_data_byjson($json);
    if (empty($requestData['data'])) {
        //echo 1;die();
        echo 'there collected datas;but not have order!'."\n";
    }
    $requestData['storeid'] = 3;
    $requestData['storeName'] = '商铺21';
    echo "第".$i."次采集"."采集时间为：".date("Y-md-d H:i:s")."\n";
    echo $requestData['data'] . "\n". "\n". "\n". "\n". "\n". "\n";
    $res = post_data($server_notify_url, $requestData);
    if(empty($argv[0])){

    }
    //sleep(2);
}