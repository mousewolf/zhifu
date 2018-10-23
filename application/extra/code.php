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

return [
    // 验证码默认驱动
    'default'   => 'Qsms',

    //邮件
    'Email'  => [
        'debug'       => '0',// SMTP调试功能 0=>关闭 1 => 错误和消息 2 => 消息
        'host'        => 'smtp.mxhichina.com',
        'port'        => '465',
        'username'    => '',
        'password'    => '',
        'address'     => '',
        'name'        => '小红帽'
    ],

    //极光
    'Jpush' => [
        'app_key'     =>  '',
        'secret_key'    =>  '',
        'options'   => [
            'disable_ssl'    =>  true
        ],
        'temp_id'    =>  '1',
    ],

    //腾讯云
    'Qsms' => [
        'app_id'     =>      '',
        'app_key'    =>      '',
        'sign_id'    =>      '',
        'sign_name'  =>      '',
        'template_id'    =>  ''
    ]
];