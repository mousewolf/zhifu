<?php


namespace app\index\controller;


class Agent extends Base
{

    /**
     * 获取代理名下商户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     * @return mixed
     */
    public function index(){
        $where = ['puid'=> is_login()];
        $this->assign('list', $this->logicUser->getUserList($where, true, 'create_time desc', 10));
       return $this->fetch();
    }


    /**
     * 下级交易明细
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     * @return mixed
     */
    public function orders(){
        return $this->fetch();
    }

    /**
     * 下级分润比例
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     * @return mixed
     */
    public function profit(){
        return $this->fetch();
    }
}