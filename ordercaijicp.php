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
        $post_str = '';
        if($i>0) {

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
$i = 0;
$last_time = 0;
while (1) {
    $i++;
    $url = "https://trade.taobao.com/trade/itemlist/list_sold_items.htm?action=itemlist/SoldQueryAction&event_submit_do_query=1&auctionStatus=SEND&tabCode=haveSendGoods";
    $url_refer = "https://trade.taobao.com/trade/";
    $cookie = "t=12d9e33956fc09b4f0cef790ecaebf73; cna=QA4JFUzoV1MCAS04mV1kiNPc; UM_distinctid=1695c9e8c192a0-094fb7e927584e-3e76035c-1fa400-1695c9e8c1a54a; tracknick=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; lgc=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; tg=0; x=e%3D1%26p%3D*%26s%3D0%26c%3D0%26f%3D0%26g%3D0%26t%3D0%26__ll%3D-1%26_ato%3D0; thw=us; v=0; _m_h5_tk=012ccf76ac03b2381cf71c7678301eea_1552364709097; _m_h5_tk_enc=8a685ef8b26c83438ebf0c96a330a1de; cookie2=1734be701721f399942a5db9581fcbee; _tb_token_=e5d7e5b3dab5e; _fbp=fb.1.1552354991190.455027382; hng=SG%7Czh-CN%7CSGD%7C702; unb=3059864953; sg=%E6%B6%A732; _l_g_=Ug%3D%3D; skt=5c6a93b72a0b71e6; cookie1=Vq9jeSVL9ZoZrM63jNovYwuOdeG2zvjie5OH%2F2CGbZ0%3D; csg=99b60e8a; uc3=vt3=F8dByEv9pxHkA%2FP8lTQ%3D&id2=UNDRzi5BgNtJDw%3D%3D&nk2=txBQSND3xVQ%3D&lg2=WqG3DMC9VAQiUQ%3D%3D; existShop=MTU1MjM1NDk5NQ%3D%3D; _cc_=V32FPkk%2Fhw%3D%3D; dnk=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; _nk_=%5Cu4E91%5Cu77F3%5Cu4ED9%5Cu6DA7; cookie17=UNDRzi5BgNtJDw%3D%3D; mt=ci=14_1; x5sec=7b2274726164656d616e616765723b32223a226462653335353939613766646536623232633564363735313262333066653332435079356e4f5146454c7a74724a4b69393776583341456144444d774e546b344e6a51354e544d374d513d3d227d; uc1=cookie14=UoTZ5iY0%2B473JA%3D%3D&lng=zh_CN&cookie16=VT5L2FSpNgq6fDudInPRgavC%2BQ%3D%3D&existShop=true&cookie21=WqG3DMC9Edo1SB5NBLjxxA%3D%3D&tag=8&cookie15=UtASsssmOIJ0bQ%3D%3D&pas=0; whl=-1%260%260%261552358720527; isg=BDAwZ8bHGLT7DcS17rJieT22AfeIxRS1u5A8VCqA-At-5dKP0o_2UfaUPY0g9cyb";
    $server_notify_url = "http://www.chncpweb.com/ownpay/notify";
    if($last_time == 0){
        $last_time = time();
    }else{
        echo time()-$last_time."\n";
        if(time()-$last_time<3){
            echo "sleep sss...."."\n";
           sleep(3-(time()-$last_time));
        }
        $last_time = time();
    }
    $html = get_html($url, $url_refer, $cookie);

    $requestData['data'] = get_data($html);

    if (empty($requestData['data'])) {
        //echo 1;die();
    }
    $requestData['storeid'] = 3;
    $requestData['storeName'] = '商铺21';
    echo "第".$i."次采集"."采集时间为：".date("Y-md-d H:i:s")."\n";
    echo $requestData['data'] . "\n". "\n". "\n". "\n". "\n". "\n";
    $res = post_data($server_notify_url, $requestData);
    //sleep(2);
}