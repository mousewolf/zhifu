<?php


namespace app\index\controller;


class Agent extends Base
{

    public function index(){
        $where = ['puid'=> is_login()];
        $this->assign('list', $this->logicUser->getUserList($where, true, 'create_time desc', 10));
       return $this->fetch();
    }

    public function orders(){
        return $this->fetch();
    }

    public function profit(){
        return $this->fetch();
    }
}