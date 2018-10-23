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

namespace app\admin\controller;


use app\common\library\enum\CodeEnum;

class Account extends BaseAdmin
{

    /**
     * 账户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index(){
        return $this->fetch();
    }

    /**
     * 账户列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getList(){
        $where = [];
        //组合搜索
        !empty($this->request->param('uid')) && $where['uid']
            = ['eq', $this->request->param('uid')];

        !empty($this->request->param('username')) && $where['username']
            = ['like', '%'.$this->request->param('username').'%'];
        $data = $this->logicUserAccount->getAccountList($where, 'a.*,b.id as b_id,b.name as bank', 'create_time desc', false);
        $this->result($data || empty($data) ? [CodeEnum::SUCCESS,'',$data] : [CodeEnum::ERROR,'暂无数据','']);
    }

    /**
     * 编辑商户账户信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function edit(){
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicUserAccount->editAccount($this->request->post()));
        //获取商户账户信息
        $this->assign('bank',$this->logicBank->getBankerList());
        $this->assign('account',$this->logicUserAccount->getAccountInfo(['id' =>$this->request->param('id')]));

        return $this->fetch();
    }
}