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


class User extends Base
{

    /**
     * 商户首页
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index(){

        return $this->fetch();
    }

    /**
     * 商户信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function info()
    {
        $this->common();

        return $this->fetch();
    }

    /**
     * 密码管理
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function password()
    {
        if($this->request->isPost()){
            if ($this->request->post('uid') == is_login()){
                $this->result($this->logicUser->changePwd($this->request->post()));
            }else{
                $this->result(0,'非法操作，请重试！');
            }
        }
        //获取商户详细信息
        $this->assign('user',$this->logicUser->getUserInfo(['uid' =>is_login()]));

        return $this->fetch();
    }

    /**
     * Common
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function common()
    {

        if($this->request->isPost()){
            if ($this->request->post('uid') == is_login()){
                $this->result($this->logicUser->editUser($this->request->post()));
            }else{
                $this->result(0,'非法操作，请重试！');
            }
        }
        //获取商户详细信息
        $this->assign('user',$this->logicUser->getUserInfo(['uid' =>is_login()]));

    }

    /**
     * 操作日志
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function log()
    {

        $where = ['uid'=> is_login()];
        //组合搜索

        $where['module'] = ['like', 'index'];

        //时间搜索  时间戳搜素
        $where['create_time'] = $this->parseRequestDate();

        $this->assign('list',$this->logicLog->getLogList($where, true, 'create_time desc',10));

        return $this->fetch();
    }

}