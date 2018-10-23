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

/**
 * 支付参数示例
 */
return [

    //微信
    'wxpay' => [
        'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wxb3fxxxxxxxxxxx', // 公众号 APPID
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 小程序 APPID
        'mch_id' => '145776xxxx',
        'key' => 'mF2suE9sU6Mk1CxxxxIxxxxx',
        'notify_url' => 'https://api.iredcap.cn/notify/wx_notify',
        'cert_client' => CRET_PATH . 'wxpay/apiclient_cert.pem', // optional, 退款，红包等情况时需要用到
        'cert_key' => CRET_PATH . 'wxpay/apiclient_key.pem',// optional, 退款，红包等情况时需要用到
        'log' => [ // optional
            'file' => RUNTIME_PATH .'./logs/wechat.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'daily', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        // 'mode' => 'dev',
    ],

    //支付宝
    'alipay'=>[
        'app_id' => '20180000000000',
        'notify_url' => 'https://api.iredcap.cn/notify/ali_notify',
        'return_url' => 'https://api.iredcap.cn/notify/ali_return',
        'ali_public_key' => 'WFNAWERGNAKWREGVAWKRG;',
        'private_key' => 'WFNAWERGNAKWREGVAWKRG',
        'log' => [ // optional
            'file' => RUNTIME_PATH . './logs/alipay.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'daily', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        // 'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ]
];
