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

use think\Route;

// admin子域名绑定到admin模块
Route::domain('admin','admin');

// api子域名绑定到api模块
Route::domain('api', function(){
    // 动态注册域名的路由规则
    Route::miss('api/Miss/index');//路由错误返回
    Route::post('pay/unifiedorder','api/Pay/unifiedorder');//网关支付
    Route::post('pay/orderquery','api/Pay/orderquery');//网关支付
    /**
     * Notify
     */
    Route::post('notify/qq_notify','api/Notify/qqNotify');//QQ异步通知
    Route::rule('notify/wx_notify','api/Notify/wxNotify');//微信异步通知
    Route::post('notify/ali_notify','api/Notify/aliNotify');//支付宝异步通知
    Route::get('notify/ali_redirect','api/Notify/aliRedirect');//支付宝同步回调


    Route::rule('test/notify','api/Test/actionNotify');
    Route::get('queue/closer/:id','api/Test/actionOrderClose');
});

Route::domain('www',function (){
    /**
     * 首页
     */
    Route::get('news/:id','index/Index/news');  //行业动态
    Route::rule('get','index/Index/get','GET|POST');  //支付API演示
    Route::get('notice/:id','index/Index/notice'); //余呗消息
    Route::get('pricing','index/Index/pricing'); //产品价格
    Route::get('download','index/Index/download'); //sdk下载
    Route::get('protocol','index/Index/protocol'); //服务条款
    Route::get('help/:id','index/Index/help'); //服务条款
    /**
     * //商户
     */
    Route::get('user','index/User/index');//商户首页
    Route::get('user/account','index/User/account');//商户基本
    Route::rule('user/edit','index/User/edit'); //商户账户信息
    Route::get('user/order','index/Order/index');
    Route::get('user/settle','index/Order/settle');
    Route::get('user/paid','index/Balance/paid');
    Route::get('user/balance','index/Balance/record');
    Route::get('user/open','index/Api/index');
    /**
     * 登录注册
     */
    Route::rule('login','index/Login/login');  //商户登陆
    Route::rule('register','index/Login/register'); //商户注册
    Route::rule('logout','index/Login/logout'); //商户注册
    //API
    Route::post('validate/can-use-user','index/Login/checkUser');
    Route::post('validate/can-use-phone','index/Login/checkPhone');
    Route::post('validate/sms','index/Login/sendSmsCode');
    Route::rule('active/sendActive','index/Login/sendActiveCode');
    Route::get('active/:code','index/Login/checkActiveCode');
    //极验
    Route::post('validate/gt-start','index/Login/startGeetest');
    Route::post('validate/gt-verify','index/Login/checkGeetest');
});
