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

class System extends BaseAdmin
{
    /**
     * 系统设置
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index()
    {
        $this->assign('list', $this->logicConfig->getConfigList());
        return $this->fetch();
    }

    /**
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function menu(){
        $this->assign('menu',$this->logicMenu->getAll());
        return $this->fetch();
    }

    /**
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function email(){
        $this->request->isPost() && $this->result(
            $this->logicConfig->settingSave(
                $this->request->param()
            )
        );
        $this->assign('email', $this->logicConfig->getConfigList(['name' => ['like',['%email%']]]));
        return $this->fetch();
    }

    /**
     * 站点基本信息修改
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function website(){
        $this->request->isPost() && $this->result(
            $this->logicConfig->settingSave(
                $this->request->param()
            )
        );
        $this->result(CodeEnum::ERROR,'未知错误');
    }
}