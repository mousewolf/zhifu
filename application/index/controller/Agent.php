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

    /**
     * 新增下级商户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     * @return mixed
     */
    public function addUser(){

        if($this->request->isPost()){
            if ($this->request->post('u/a')['puid'] == is_login()){
                //戴创建商户
                $this->result($this->logicLogin->doregister($this->request->post('u/a')));
            }else{
                $this->result(0,'非法操作，请重试！');
            }
        }
        //银行
        $this->assign('banker', $this->logicBanker->getBankerList());

        return $this->fetch();
    }

}