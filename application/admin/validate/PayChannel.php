<?php
/**
 * +----------------------------------------------------------------------
 *   | 草帽支付系统 [ WE CAN DO IT JUST THINK ]
 * +----------------------------------------------------------------------
 *   | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 * +----------------------------------------------------------------------
 *   | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 *   | Author: Brian Waring <BrianWaring98@gmail.com>
 * +----------------------------------------------------------------------
 */

namespace app\admin\validate;

class PayChannel extends BaseAdmin
{
    // 验证规则
    protected $rule = [
        'name'  => 'require|length:3,10',
        'rate'  => 'require|number',
        'daily'     => 'require|number',
        'remarks'    => 'require',
        'param'    => 'require'
    ];

    // 验证提示
    protected $message = [
        'name.require'    => '渠道名不能为空',
        'name.length'     => '渠道名长度为6-30个字符之间',
        'rate.require'    => '渠道费率不能为空',
        'rate.number'     => '渠道费率必须为数字',
        'daily.require'       => '渠道日限不能为空',
        'daily.number'       => '渠道日限必须为数字',
        'remarks.require'     => '渠道备注不能为空',
        'param.require'      => '支付配置不能为空'
    ];
}