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
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36");
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
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
     //   $order_num = '376461794668044623';
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
$cookie = "_uab_collina=155235502656204876899903; swfstore=191407; t=12d9e33956fc09b4f0cef790ecaebf73; cna=QA4JFUzoV1MCAS04mV1kiNPc; UM_distinctid=1695c9e8c192a0-094fb7e927584e-3e76035c-1fa400-1695c9e8c1a54a; tracknick=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; lgc=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; tg=0; x=e%3D1%26p%3D*%26s%3D0%26c%3D0%26f%3D0%26g%3D0%26t%3D0%26__ll%3D-1%26_ato%3D0; thw=us; v=0; cookie2=1734be701721f399942a5db9581fcbee; _tb_token_=e5d7e5b3dab5e; _fbp=fb.1.1552354991190.455027382; hng=SG%7Czh-CN%7CSGD%7C702; unb=3059864953; sg=%E6%B6%A732; _l_g_=Ug%3D%3D; skt=5c6a93b72a0b71e6; cookie1=Vq9jeSVL9ZoZrM63jNovYwuOdeG2zvjie5OH%2F2CGbZ0%3D; csg=99b60e8a; uc3=vt3=F8dByEv9pxHkA%2FP8lTQ%3D&id2=UNDRzi5BgNtJDw%3D%3D&nk2=txBQSND3xVQ%3D&lg2=WqG3DMC9VAQiUQ%3D%3D; existShop=MTU1MjM1NDk5NQ%3D%3D; _cc_=V32FPkk%2Fhw%3D%3D; dnk=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; _nk_=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; cookie17=UNDRzi5BgNtJDw%3D%3D; mt=ci=12_1; _m_h5_tk=61d37aef36b6497b02fce1a861494e8b_1552383793009; _m_h5_tk_enc=cb3c11085c62632d55c9f6f57b70721a; x5sec=7b2274726164656d616e616765723b32223a223034636138353432323138363638333061616132653566663964336361356138434b44416e65514645494b38326661507463375a64686f4d4d7a41314f5467324e446b314d7a7378227d; l=AiQkkP/L0Nv0IYEWuqa82AKMdCkWuEgn; uc1=cart_m=0&cookie14=UoTZ5iY22HAzSw%3D%3D&lng=zh_CN&cookie16=W5iHLLyFPlMGbLDwA%2BdvAGZqLg%3D%3D&existShop=true&cookie21=WqG3DMC9Edo1SB5NBLjxxA%3D%3D&tag=8&cookie15=WqG3DMC9VAQiUQ%3D%3D&pas=0; apush5683dfa288f6e335a2f96a10a95691e1=%7B%22ts%22%3A1552376694582%2C%22parentId%22%3A1552376678522%7D; whl=-1%260%260%261552376695008; isg=BPj4HW6u0NwNwjxYghYpSBNgya9KyVz8Mwhk_DJoMjOcTZs323Ore2grBQXYHRTD";

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
        if(time()-$last_time<3){
          //  echo "sleep sss...."."\n";
         //  sleep(3-(time()-$last_time));
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

    //sleep(2);
}