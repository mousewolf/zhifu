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

namespace app\admin\logic;
use app\common\library\enum\CodeEnum;

/**
 * 权限组逻辑
 */
class AuthGroup extends BaseAdmin
{

    /**
     * 获取权限分组列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param bool $paginate
     * @return mixed
     */
    public function getAuthGroupList($where = [], $field = true, $order = '', $paginate = false)
    {
        return $this->modelAuthGroup->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 权限组添加
     */
    public function groupAdd($data = [])
    {
        
        $validate_result = $this->validateAuthGroup->scene('add')->check($data);
        
        if (!$validate_result) {
            
            return [CodeEnum::ERROR, $this->validateAuthGroup->getError()];
        }
        
        $url = url('groupList');
        
        $data['uid'] = is_admin_login();
        
        $result = $this->modelAuthGroup->setInfo($data);
        
        return $result ? [CodeEnum::SUCCESS, '权限组添加成功', $url] : [CodeEnum::ERROR, $this->modelAuthGroup->getError()];
    }
    
    /**
     * 权限组编辑
     */
    public function groupEdit($data = [])
    {
        
        $validate_result = $this->validateAuthGroup->scene('edit')->check($data);
        
        if (!$validate_result) {
         
            return [CodeEnum::ERROR, $this->validateAuthGroup->getError()];
        }
        
        $url = url('groupList');
        
        $result = $this->modelAuthGroup->setInfo($data);

        return $result ? [CodeEnum::SUCCESS, '权限组编辑成功', $url] : [CodeEnum::ERROR, $this->modelAuthGroup->getError()];
    }
    
    /**
     * 权限组删除
     */
    public function groupDel($where = [])
    {
        
        $result = $this->modelAuthGroup->deleteInfo($where);

        return $result ? [CodeEnum::SUCCESS, '权限组删除成功'] : [CodeEnum::ERROR, $this->modelAuthGroup->getError()];
    }
    
    /**
     * 获取权限组信息
     */
    public function getGroupInfo($where = [], $field = true)
    {
        
        return $this->modelAuthGroup->getInfo($where, $field);
    }

    /**
     * 设置用户组权限节点
     */
    public function setGroupRules($data = [])
    {
        
        $data['rules'] = !empty($data['rules']) ? implode(',', array_unique($data['rules'])) : '';
        
        $url = url('groupList');
        
        $result = $this->modelAuthGroup->setInfo($data);
        
        if ($result) {

            $this->updateSubAuthByGroup($data['id']);

            return [CodeEnum::SUCCESS, '权限设置成功', $url];
        } else {
            
            return [CodeEnum::ERROR, $this->modelAuthGroup->getError()];
        }
    }
    
    /**
     * 选择权限组
     */
    public function selectAuthGroupList($group_list = [], $member_group_list = [])
    {
        
        $member_group_ids = array_extract($member_group_list, 'group_id');
        
        foreach ($group_list as &$info) {
            
            in_array($info['id'], $member_group_ids) ? $info['tag'] = 'active' :  $info['tag'] = '';
        }
            
        return $group_list;
    }
    
    /**
     * 递归更新下级权限节点，确保下级权限不能超越上级
     * 若上级某权限被收回，则下级对应的权限同样被收回
     * 按会员更新
     */
    public function updateSubAuthByMember($uid = 0)
    {
        
        $group_list = $this->logicAuthGroupAccess->getMemberGroupInfo($uid);
        
        $rules_str_list = array_extract($group_list, 'rules');
        
        $rules_array_list = array_map("str2arr", $rules_str_list);
        
        $rules_array = [];
        
        foreach ($rules_array_list as $v) {
            
            $rules_array = array_merge($rules_array, $v);
        }
        
        // 当前授权会员的所有权限节点数组
        $rules_unique_array = array_unique($rules_array);
        
        $sub_uids = $this->logicMember->getSubMemberIds($uid);
        
        $sub_group_list = $this->logicAuthGroupAccess->getMemberGroupInfo($sub_uids);
        
        // 所有下级的权限组id集合
        $sub_group_ids = array_unique(array_extract($sub_group_list, 'group_id'));
        
        $this->updateGroupRulesByStandard($rules_unique_array, $sub_group_ids);
    }
    
    /**
     * 递归更新下级权限节点，确保下级权限不能超越上级
     * 若上级某权限被收回，则下级对应的权限同样被收回
     * 按权限组更新
     */
    public function updateSubAuthByGroup($group_id = 0)
    {
        
        $group_list = $this->logicAuthGroupAccess->getAuthGroupAccessList(['group_id' => $group_id]);
        
        $member_arr_ids = array_unique(array_extract($group_list, 'uid'));
        
        foreach ($member_arr_ids as $id) {
            
            $this->updateSubAuthByMember($id);
        }
    }
    
    /**
     * 按参数$standard_rules_array权限节点数组标准，将参数$group_ids权限组ID数组下的权限节点全部更新
     */
    public function updateGroupRulesByStandard($standard_rules_array = [], $group_ids = [])
    {
        
        $group_list = $this->getAuthGroupList(['id' => ['in', $group_ids]]);
        
        foreach ($group_list as $v)
        {
            
            $rules_arr = str2arr($v['rules']);
            
            foreach ($rules_arr as $kk => $vv)
            {
                if (!in_array($vv, $standard_rules_array)) {
                    
                    unset($rules_arr[$kk]);
                }
            }
            
            $v['rules'] = arr2str(array_values($rules_arr));
            
            $this->modelAuthGroup->setFieldValue(['id' => $v['id']], 'rules', $v['rules']);
        }
    }
}
