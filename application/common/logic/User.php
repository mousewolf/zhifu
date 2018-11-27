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

namespace app\common\logic;

use app\common\library\enum\CodeEnum;
use think\Db;
use think\Log;
use think\Validate;

class User extends BaseLogic
{
    /**
     * 获取商户列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param string $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getUserList($where = [], $field = '*', $order = '', $paginate = 20)
    {
        $this->modelUser->limit = !$paginate;
        $this->modelUserAccount->alias('a');

        $join = [
            ['admin_group b', 'a.uid = b.id'],
        ];

        $this->modelUserAccount->join = $join;
        return $this->modelUser->getList($where, $field, $order, $paginate);
    }

    /**
     * 获取商户认证列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param string $field
     * @param string $order
     * @param int $paginate
     *
     * @return mixed
     */
    public function getUserAuthList($where = [], $field = '*', $order = '', $paginate = 20)
    {
        $this->modelUserAuth->limit = !$paginate;
        return $this->modelUserAuth->getList($where, $field, $order, $paginate);
    }

    /**
     * 获取用户总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @return mixed
     */
    public function getUserCount($where = []){
        return $this->modelUser->getCount($where);
    }

    /**
     * 获取用户认证数据总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @return mixed
     */
    public function getUserAuthCount($where = []){
        return $this->modelUserAuth->getCount($where);
    }

    /**
     * 获取商户信息详情
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @return mixed
     */
    public function getUserInfo($where = [], $field = true)
    {
        return $this->modelUser->getInfo($where, $field);
    }

    /**
     * 获取认证信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     *
     * @return mixed
     */
    public function getUserAuthInfo($where = [], $field = true){
        return $this->modelUserAuth->getInfo($where, $field);
    }

    /**
     * 添加一个商户
     * @author 勇敢的小笨羊
     * @param $data
     * @return array|string
     */
    public function addUser($data){
        //TODO 数据验证
        $validate = $this->validateUserValidate->scene('add')->check($data);

        if (!$validate) {
            return ['code' => CodeEnum::ERROR, 'msg' => $this->validateUserValidate->getError()];
        }
        //TODO 添加数据
        Db::startTrans();
        try{
            //密码
            $data['password'] = data_md5_key($data['password']);
            //生成安全码
            $data['code']   = getRandChar();
            //基本信息
            $user = $this->modelUser->setInfo($data);
            //账户记录
            $this->modelUserAccount->setInfo(['uid'  => $user ]);
            //资金记录
            $this->modelBalance->setInfo(['uid'  => $user ]);
            //生成API记录
            $this->modelApi->setInfo([
                'uid'  => $user,
                'domain' =>  $data['siteurl'],
                'sitename' =>  $data['sitename']
            ]);

            //加入邮件队列
            $jobData = $this->getUserInfo(['uid'=>$user],'uid,account,username');

            //邮件场景
            $jobData['scene']   = 'register';
            $this->logicQueue->pushJobDataToQueue('AutoEmailWork' , $jobData , 'AutoEmailWork');

            action_log('新增', '新增商户。UID:'. $user);

            Db::commit();
            return ['code' => CodeEnum::SUCCESS, 'msg' => '添加商户成功'];
        }catch (\Exception $ex){
            Db::rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => $ex->getMessage()];
        }

    }

    /**
     * 编辑商户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @return array
     */
    public function editUser($data){

        //TODO  验证数据
        $validate = $this->validateUserValidate->scene('edit')->check($data);

        if (!$validate) {

            return ['code' => CodeEnum::ERROR, 'msg' => $this->validateUserValidate->getError()];
        }
        //TODO 修改数据
        Db::startTrans();
        try{
                if (empty($data['password'])){
                    unset($data['password']);
                }else{
                    $data['password'] = data_md5_key($data['password']);
                }
                $this->modelUser->setInfo($data);

                action_log('修改', '修改个人信息。'. arr2str($data));

            Db::commit();
            return ['code' => CodeEnum::SUCCESS, 'msg' => '编辑成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return ['code' => CodeEnum::ERROR, 'msg' => '未知错误'];
        }
    }

    /**
     * 认证信息保存
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     *
     * @return array
     */
    public function saveUserAuth($data){
        //TODO  验证数据
        $validate = $this->validateUserAuth->check($data);

        if (!$validate) {

            return ['code' => CodeEnum::ERROR, 'msg' => $this->validateUserAuth->getError()];
        }
        //TODO 修改数据
        Db::startTrans();
        try{

            if(!empty($data['card']))  $data['card'] = json_encode(array_values($data['card']));

            $this->modelUserAuth->setInfo($data);

            action_log('提交', '个人信息认证。');

            Db::commit();
            return ['code' => CodeEnum::SUCCESS, 'msg' => '编辑成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return ['code' => CodeEnum::ERROR, 'msg' => '未知错误'];
        }
    }


    /**
     * 修改密码
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     *
     * @return array
     */
    public function changePwd($data){
        //TODO  验证数据
        $validate = $this->validatePassword->check($data);

        if (!$validate) {

            return ['code' => CodeEnum::ERROR, 'msg' => $this->validatePassword->getError()];
        }

        //查询用户
        $user = $this->getUserInfo(['uid' => is_login()],'password');

        //验证原密码
        if ( $user && data_md5_key($data['oldpassword']) == $user['password']) {

            $result = $this->setUserValue(['uid' => is_login()], 'password', data_md5_key($data['password']));

            action_log('修改', '修改密码');

            return $result && !empty($result) ? ['code' => CodeEnum::SUCCESS, 'msg' => '修改密码成功']
                : ['code' => CodeEnum::ERROR, 'msg' => '修改失败'];
        }else{
            return ['code' => CodeEnum::ERROR, 'msg' => '原密码不正确'];
        }
    }

    /**
     * 删除商户
     * @author 勇敢的小笨羊
     * @param array $where
     * @return array
     */
    public function delUser($where = []){
        Db::startTrans();
        try{
            $this->modelUser->deleteInfo($where);
            $this->modelUserAccount->deleteInfo($where);
            $this->modelBalance->deleteInfo($where);
            $this->modelApi->deleteInfo($where);
            Db::commit();
            return ['code' => CodeEnum::SUCCESS, 'msg' => '会员删除成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return ['code' => CodeEnum::ERROR, 'msg' => '未知错误'];
        }
    }


    /**
     * 设置管理员信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param string $field
     * @param string $value
     * @return mixed
     */
    public function setUserValue($where = [], $field = '', $value = '')
    {
        return $this->modelUser->setFieldValue($where, $field, $value);
    }

    /**
     * 改变商户可用性
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @param int $value
     * @return array
     */
    public function setUserStatus($where,$value = 0){
        Db::startTrans();
        try{
            $this->setUserValue($where, $field = 'status', $value);
            Db::commit();
            return ['code' => CodeEnum::SUCCESS, 'msg' => '修改状态成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return ['code' => CodeEnum::ERROR, 'msg' => '未知错误'];
        }
    }
}