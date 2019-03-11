<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-11
 * Time: 上午10:24
 */
namespace app\ownpay\controller;

use app\ownpay\model\OwnpayOrder;
use think\Controller;
use think\helper\Time;
use think\Db;
use think\Log;
use think\Model;
class Index extends Controller
{
    //展示所有的订单
    public function lists()
    {
        $modelOrders = new OwnpayOrder();
        $where = array();
        $datas = $modelOrders::all();
        $this->assign('datas',$datas);
        $pay_url = "http://".$_SERVER['SERVER_NAME']."/ownpay/pay"."?id=";
        $this->assign('datas',$datas);
        $this->assign('pay_url',$pay_url);
        return $this->fetch();
    }
    //用户添加订单
    public  function add()
    {
        return $this->fetch();
    }
    public  function getstatus()
    {

        $where['id']= intval($_POST['id']);
        $result =  Db::table('cm_ownpay_order')
            ->where($where)->find();
        $array['status'] =$result['status'];
        echo json_encode($array); die();
    }
    public  function addDo()
    {
        //判断订单号是否已经存在
        $orderNum = $_POST['orderNum'];

        if(empty( $orderNum)){
            $this->error('订单号码不能为空','/ownpay/add',3);
        }
        $where['orderNum']= $orderNum;
        $result =  Db::table('cm_ownpay_order')
            ->where($where)->find();
        if( $result['status']!=0){
            $this->error('该订单二维码已经上传完成','/ownpay/add',3);
        }
        if(empty($result)){
            $this->error('订单还未生成请稍后重试，或者请检测订单号码是否正确','/ownpay/add',3);
        }

        if (( ($_FILES["file"]["type"] == "image/jpeg")
                || ($_FILES["file"]["type"] == "image/pjpeg")
                || ($_FILES["file"]["type"] == "image/png"))
            && ($_FILES["file"]["size"] < 2000000))
        {
            if ($_FILES["file"]["error"] > 0)
            {

                $this->error("Return Code: " . $_FILES["file"]["error"],3);
            }
            else
            {
                if($_FILES["file"]["type"] == "image/jpeg"){
                    $ext = '.jpg';
                }
                if($_FILES["file"]["type"] == "image/pjpeg"){
                    $ext = '.jpg';
                }
                if($_FILES["file"]["type"] == "image/png"){
                    $ext = '.png';
                }
                $name = md5(microtime()).$ext;
                if (file_exists("uploads/" . $name))
                {
                    echo $_FILES["file"]["name"] . " already exists. ";
                }
                else
                {
                    move_uploaded_file($_FILES["file"]["tmp_name"],
                        "uploads/" .$name);
                  //  $this->success("Stored in: " . "upload/" . $_FILES["file"]["name"],'/ownpay/add',3);
                }
            }
        }
        else
        {
            $this->error('非法图片，请选择二维码图片','/ownpay/add',3);
        }
        $qr_image = $name;
        Db::table('cm_ownpay_order')->where('orderNum', $orderNum)->update(['qr_image' => $qr_image,'status'=>1]);
        $this->success('操作完成','/ownpay/add',3);
    }
    public  function pay()
    {
        //需要一个key

        $where['id']= intval($_GET['id']);
        $result =  Db::table('cm_ownpay_order')
            ->where($where)->find();
        if(  empty($result)||$result['status'] !=1 ){
            $this->error('非法请求','/ownpay/add',3);
        }

        $qr_img = "/uploads/".$result['qr_image'];
        $id = $result['id'];
        $posturl = "http://".$_SERVER['SERVER_NAME']."/ownpay/getStatus";
        $return_url = "http://".$_SERVER['SERVER_NAME']."/ownpay/list";
        $this->assign('qr_img',$qr_img);
        $this->assign('id',$id);
        $this->assign('posturl',$posturl);
        $this->assign('return_url',$return_url);

        return $this->fetch();
    }
    public  function notify()
    {
        if (isset($_POST['data'])) {
            $datas = explode(",", $_POST['data']);
            foreach ($datas as $data) {
                $time = time();
                $a = explode("_____", $data);
                if(empty($a[0]) || empty($a[1])){
                    return 'error';
                }
                $orderNum = $a[0];
                $orderPrice = $a[1];
                $username = self::unicodeDecode($a[2]);
                $storeid = $_POST['storeid'];
                $storeName = $_POST['storeName'];
                $modelOrders = new OwnpayOrder();

                if ('undefined' !== $orderNum && !empty($orderNum)) {
                    if (!self::check_exsit($orderNum)) {
                        $sql = "insert into cm_ownpay_order (`orderNum`,`username`,`orderPrice`,`addTime`,`storeid`,`storeName`) values ('$orderNum','$username','$orderPrice','$time','$storeid','$storeName')";
                        $result = $modelOrders::query($sql);
                    }
                }
            }
        }
    }
    public  function paynotify(){
       // $_POST['data'] = "33333_____11111111______aaaaaaaaaaaa";
        if (isset($_POST['data'])) {
            $datas = explode(",", $_POST['data']);
            foreach ($datas as $data) {
                $time = time();
                $a = explode("_____", $data);
                if(empty($a[0]) || empty($a[1])){
                    return 'error';
                }
                $orderNum = $a[0];
                $modelOrders = new OwnpayOrder();
                if ('undefined' !== $orderNum && !empty($orderNum)) {
                    if (!self::check_pay($orderNum)) {
                        Db::table('cm_ownpay_order')->where('orderNum', $orderNum)->update(['status'=>2,'payTime'=>time()]);
                    }
                }
            }
        }
    }
     private  function unicodeDecode($unicode_str){
        $json = '{"str":"'.$unicode_str.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }
    function check_pay($data){
        $where['orderNum']= $data;
        $where['status']= 1;
        $result =  Db::table('cm_ownpay_order')
            ->where($where)->find();
        if(!empty($result)){
            return false;
        }else{
            return true;
        }

    }
    function check_exsit($data){
            $where['orderNum']= $data;
            $result =  Db::table('cm_ownpay_order')
            ->where($where)->find();
            if(empty($result)){
                return false;
            }else{
                return true;
            }

    }


}