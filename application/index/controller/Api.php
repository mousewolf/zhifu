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


class Api extends Base
{

    /**
     * 接口基本
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index(){
        $this->apiCommon();
        return $this->fetch();
    }

    /**
     * 编辑接口信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function edit(){
        $this->apiCommon();
        return $this->fetch();
    }

    /**
     * API公共
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function apiCommon(){
        $this->request->isPost() && $this->result(
            $this->logicApi->editApi(
                $this->request->post(),
                ['uid' => is_login()]
            )
        );
        $this->assign('api',$this->logicApi->getApiInfo(['uid' => is_login()]));
    }

}