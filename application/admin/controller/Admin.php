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

class Admin extends BaseAdmin
{
    public function index(){
        return $this->fetch();
    }
    /**
     * 获取管理员信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function profile(){
        $this->assign('info',$this->logicAdmin->getAdminInfo(['id' =>is_admin_login()]));
        return $this->fetch();
    }

    /**
     * 权限组列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function group()
    {
        return $this->fetch();
    }

    /**
     * 获取权限组列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function groupList()
    {
        $data = $this->logicAuthGroup->getAuthGroupList();

        $this->result($data || !empty($data) ? [CodeEnum::SUCCESS,'',$data] : [CodeEnum::ERROR,'暂无数据','']);

    }

    /**
     * 权限组添加
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function groupAdd()
    {

        $this->request->isPost() && $this->result($this->logicAuthGroup->groupAdd($this->request->post()));

        return $this->fetch('group_edit');
    }

    /**
     * 权限组编辑
     */
    public function groupEdit()
    {
        $this->request->isPost() && $this->result($this->logicAuthGroup->groupEdit($this->request->post()));

        $info = $this->logicAuthGroup->getGroupInfo(['id' => $this->request->param('id')]);

        $this->assign('info', $info);

        return $this->fetch();
    }

    /**
     * 权限组删除
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param int $id
     */
    public function groupDel($id = 0)
    {

        $this->result($this->logicAuthGroup->groupDel(['id' => $id]));
    }

    /**
     * 菜单授权
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function menuAuth()
    {

        $this->request->isPost() && $this->result($this->logicAuthGroup->setGroupRules($this->request->post()));

        // 获取未被过滤的菜单树
        $menu_tree = $this->logicBaseAdmin->getListTree($this->authMenuList);

        // 菜单转换为多选视图，支持无限级
        $menu_view = $this->logicMenu->menuToCheckboxView($menu_tree);
        //halt($menu_view);
        $this->assign('list', $menu_view);

        $this->assign('id', $this->request->param('id'));

        return $this->fetch();
    }

}