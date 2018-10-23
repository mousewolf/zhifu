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

namespace app\index\logic;

use app\common\library\Activation;
use app\common\library\enum\CodeEnum;
use app\common\library\enum\UserStatusEnum;
use think\Db;
use think\Log;

class Login extends Base
{
    /**
     * 登录操作
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $username 账号
     * @param string $password  密码
     * @return array
     */
    public function dologin($username,$password){

        $validate = $this->validateLogin->check(compact('username','password'));

        if (!$validate) {

            return [ CodeEnum::ERROR, $this->validateLogin->getError()];
        }

        $user = $this->logicUser->getUserInfo(['account' => $username]);

        //密码判断
        if (!empty($user['password']) && data_md5_key($password) == $user['password']) {
            //激活判断
            if ($user['is_verify'] == UserStatusEnum::DISABLE){
                return [ CodeEnum::ERROR,  '账号未激活,<span onclick="page(\'发送激活邮件\',\'/active/sendActive\',this,\'440px\',\'180px\')">点击发送激活邮件</span>'];
            }
            //禁用判断
            if ($user['status'] == UserStatusEnum::DISABLE){
                return [ CodeEnum::ERROR,  '账号禁用'];
            }
            $this->modelUser->setFieldValue(['uid' => $user['uid']], 'update_time', time());

            $auth = ['uid' => $user['uid'], 'update_time'  =>  time()];

            session('user_info', $user);
            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));

            return [ CodeEnum::SUCCESS, '登录成功'];

        } else {

            return [ CodeEnum::ERROR, empty($user['uid']) ? '用户账号不存在' : '密码输入错误'];
        }
    }

    /**
     * 用户注册
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $data 注册数据
     * @return array
     */
    public function doregister($data){

        $validate = $this->validateRegister->check($data);
        //数据检验
        if (!$validate) {

            return [ CodeEnum::ERROR, $this->validateRegister->getError()];
        }
        //TODO 添加数据
        Db::startTrans();
        try{
            //创建基本  是否修改
            if (empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = data_md5_key($data['password']);
            }
            $user = $this->modelUser->setInfo($data);
            //创建账号
            $this->modelUserAccount->setInfo(['uid'  => $user ]);
            //创建账户
            $this->modelBalance->setInfo(['uid'  => $user ]);
            //创建API
            $this->modelApi->setInfo(['uid'  => $user]);

            //加入邮件队列
            $jobData = $this->logicUser->getUserInfo(['uid'=>$user],'uid,account,username');
            //邮件场景
            $jobData['scene']   = 'register';
            $this->logicQueue->pushJobDataToQueue('AutoEmailWork' , $jobData , 'AutoEmailWork');

            Db::commit();
            return [CodeEnum::SUCCESS,'注册成功'];
        }catch (\Exception $ex){
            Db::rollback();
            return [ CodeEnum::ERROR,$ex->getMessage()];
        }
    }
    /**
     * 数据检测
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $field
     * @param string $value
     * @return mixed
     */
    public function checkField($field='',$value=''){
        $user_field = $this->modelUser->getInfo([$field=>$value], $field);
        if($user_field){
            return [ CodeEnum::ERROR, '账户已被使用'];
        }else{
            return [ CodeEnum::SUCCESS, '账户可用'];
        }
    }

    /**
     * 发送激活邮件
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $account
     * @return array
     */
    public function sendActiveCode($account){
        $user = $this->logicUser->getUserInfo(['account'=>$account]);
        if (!$user){
            return [ CodeEnum::ERROR, '注册邮箱不存在'];
        }else{
            if (($user['status'] && $user['is_verify'] ) == UserStatusEnum::ENABLE){
                return [ CodeEnum::ERROR, '商户已激活'];
            }
            $user['scene']  = 'register';
            //加入邮件队列
            $this->logicQueue->pushJobDataToQueue('AutoEmailWork' , $user , 'AutoEmailWork');
            return [ CodeEnum::SUCCESS,'发送成功'];
        }
    }

    /**
     * 商户激活过程
     * 1.获取参数  比对商户是否存在
     * 2.商户存在  验证是否已经激活过了（这注意  激活链接激活成功之后 后续直接跳转登录页面）
     * 3.code校验
     * 4.End
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $code
     * @return array|mixed
     */
    public function activationCode($code){

        //验证code可用性 并返回注册商户数据对象
        $Verification = (new Activation())->VerificationActiveCode($code);
        if (!$Verification){
            return ['code'=>'0','errmsg'=>'激活链接失效了，请重新发送'];
        }

        //TODO 验证逻辑

        $user = $this->modelUser->getUser($Verification->uid);
        if (!$user) {
            return ['code'=> CodeEnum::ERROR,'errmsg'=>'商户不存在！'];
        } else {
            //是否已经激活
            if(($user['status'] && $user['is_verify'] ) == UserStatusEnum::ENABLE){
                return ['code'=> CodeEnum::SUCCESS,'errmsg'=>'商户已经激活过了 :-)'];
            }else{
                $this->modelUser->updateInfo(
                    ['uid'=>$Verification->uid],
                    [
                        'status' => UserStatusEnum::ENABLE,
                        'is_verify' => UserStatusEnum::ENABLE,
                        'is_verify_phone' => UserStatusEnum::ENABLE
                    ]);
                return ['code'=> CodeEnum::SUCCESS,'errmsg'=>'商户激活成功！'];
            }

        }

    }

    /**
     * 注销当前用户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return string
     */
    public function logout()
    {

        clear_user_login_session();

        return url('index/login/login');
    }
}