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


function get_html($url,$url_refer,$cookie)
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
    $content = curl_exec($ch);
    return $content;
}
function get_data($html){
    $result = explode("var data = JSON.parse('",$html);
    $result2 = explode("]}');",$result[1]);
    $result3 = explode("mainOrders",$result2[0]);
    $post = '';
    $result4 = explode("\\\"guestUser\\\":",$result3[1]);
    for($i =0;$i<count($result4);$i++){

        if($i>0) {

            $post_str = '';
            $str = $result4[$i];
            $b = explode( "_input_charset=utf-8&orderid=",$str);
            $num = substr($b[1],0, 18);
            $post_str = $num;
            $priceregexp = "/realTotal\\\\\":\\\\\"(.*?)\\\\\"/";
            $match ='';
            preg_match($priceregexp, $str, $match);
            $price = $match[1];
            $post_str = $post_str.'_____'.$price;
            $usernameexp = "/userID\=(.*)\&sign\=/";
            preg_match($usernameexp,$str,$match);
            $username = $match[1];
            $post_str = $post_str.'_____'.$username;
        }
        if($i==1){
            if($num!='undefined'){
                $post = $post_str;
            }
        }else{
            $post = $post.','.$post_str;
        }
    }

    return $post;
}

$url = "https://trade.taobao.com/trade/itemlist/list_sold_items.htm";
$url_refer = "https://trade.taobao.com/trade/";
$cookie = "t=12d9e33956fc09b4f0cef790ecaebf73; cna=QA4JFUzoV1MCAS04mV1kiNPc; _m_h5_tk=4a25baaf60beaa099c9794f8c3ee1775_1552046780997; _m_h5_tk_enc=436dc51deb04dda42c2201f279c360d0; UM_distinctid=1695c9e8c192a0-094fb7e927584e-3e76035c-1fa400-1695c9e8c1a54a; cookie2=184140b2fe777125ad2a43d7b650954a; v=0; _tb_token_=58f18d3e3eef1; unb=3059864953; sg=%E6%B6%A732; _l_g_=Ug%3D%3D; skt=00692f644b33ce3a; cookie1=Vq9jeSVL9ZoZrM63jNovYwuOdeG2zvjie5OH%2F2CGbZ0%3D; csg=817365e7; uc3=vt3=F8dByEv8z9EZWm%2FH7Ko%3D&id2=UNDRzi5BgNtJDw%3D%3D&nk2=txBQSND3xVQ%3D&lg2=Vq8l%2BKCLz3%2F65A%3D%3D; existShop=MTU1MjI5MjgwNw%3D%3D; tracknick=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; lgc=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; _cc_=UtASsssmfA%3D%3D; dnk=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; _nk_=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; cookie17=UNDRzi5BgNtJDw%3D%3D; tg=0; mt=np=; x=e%3D1%26p%3D*%26s%3D0%26c%3D0%26f%3D0%26g%3D0%26t%3D0%26__ll%3D-1%26_ato%3D0; uc1=cookie14=UoTZ5icHXSBohw%3D%3D&lng=zh_CN&cookie16=WqG3DMC9UpAPBHGz5QBErFxlCA%3D%3D&existShop=true&cookie21=V32FPkk%2Fhodroid0QoKbqw%3D%3D&tag=8&cookie15=V32FPkk%2Fw0dUvg%3D%3D&pas=0; whl=-1%260%260%261552292840311; isg=BKCgD3Zr6GXNlFRl_mLSKc1mcad4FYQ5q0CM5BqxSrtNFUE_wrjtAyGkrf0wpTxL";
$server_notify_url = "http://www.chncpweb.com/ownpay/notify";

$html = get_html($url,$url_refer,$cookie);
$requestData['data']=get_data($html);

if(empty($requestData['data'])){
    echo 1;die();
}
$requestData['storeid'] = 3;
$requestData['storeName'] = '商铺21';
$res = post_data($server_notify_url,$requestData);
var_dump($res);die();