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


class Menu extends BaseAdmin
{
    /**
     * 菜单列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index()
    {
        $this->assign('menu',$this->logicMenu->getAll());

        return $this->fetch();
    }

    /**
     * 菜单添加
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function add()
    {
        $this->menuCommon();

        return $this->fetch();
    }

    /**
     * 菜单编辑
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function edit(){
        $this->menuCommon();
        // 获取此菜单数据
        $this->assign('menu', $this->logicMenu->getMenuInfo($this->request->param('id')));

        return $this->fetch();
    }

    /**
     * 菜单添加与编辑通用方法
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function menuCommon()
    {
        $this->request->isPost() && $this->result($this->logicMenu->editMenu($this->request->post()));
        // 列出父级
        $this->assign('father',$this->logicMenu->getFather());
    }

    /**
     * 修改菜单状态
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param int $id
     * @param bool $status
     */
    public function changeStatus($id = 0,$status = false)
    {

        $this->result($this->logicMenu->setMenuStatus(['id'=>$id], $status == 1 ? '0': '1'));
    }

    /**
     * 删除菜单
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function del(){
        // post 是提交数据
        $this->request->isPost() && $this->result(
            $this->logicMenu->delMenu(
                [
                    'id' => $this->request->param('id')
                ])
        );
        // get 直接报错
        $this->result([0,'未知错误']);
    }

}