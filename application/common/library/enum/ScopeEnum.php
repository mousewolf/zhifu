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

namespace app\common\library\enum;

/**
 * 接口访问权限枚举
 * 这种权限涉及是逐级式
 * 简单，但不够灵活
 * 最完整的权限控制方式是作用域列表式权限
 * 给每个令牌划分到一个SCOPE作用域，每个作用域
 * 可访问多个接口
 */
class ScopeEnum
{
    // 普通商户权限
    const User  = 16;
    // 合作商户权限
    const Merch = 24;
    // 管理员是给CMS准备的权限
    const Super = 32;
}