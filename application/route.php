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

Route::get('notify/person/api/setting','api/Notify/person');//个人收款异步通知


// admin子域名绑定到admin模块
Route::domain('admin','admin');

// api子域名绑定到api模块
Route::domain('api', function(){
    /**
     * Pay
     */
    Route::miss('api/Miss/index');//路由错误返回
    Route::post('pay/unifiedorder','api/Pay/unifiedorder');//统一下单
    Route::post('pay/orderquery','api/Pay/orderquery');//查询订单
    Route::rule('Cashier','api/Pay/cashier');//收银台
    /**
     * Notify
     */
    Route::post('notify/:channel','api/Notify/handle');//异步通知


    Route::rule('test/notify','api/Test/actionNotify');
    Route::get('queue/closer/:id','api/Test/actionOrderClose');
});

// www子域名绑定到index模块
Route::domain('www',function () {

    /**
     * 首页
     */
    Route::get('products', 'index/Index/products');  //支付产品
    Route::get('doc', 'index/Index/document');  //支付API文档
    Route::get('demo', 'index/Index/demo');  //支付API演示
    Route::get('introduce', 'index/Index/introduce');  //接入指南
    Route::get('sdk', 'index/Index/sdk'); //sdk下载
    Route::get('protocol', 'index/Index/protocol'); //服务条款
    Route::get('help/:id', 'index/Index/help');
    Route::post('vercode','index/Index/sendVerCode'); //【测试】

    Route::get('user/getOrderStat','index/User/getOrderStat');
    /**
     * 商户
     */
    Route::get('user','index/User/index');
    Route::rule('user/info','index/User/info','GET|POST');
    Route::rule('user/auth','index/User/auth','GET|POST');
    Route::rule('user/password','index/User/password','GET|POST');
    Route::get('user/log','index/User/log');
    Route::get('user/notice/:id','index/User/notice');
    /**
     * 资金
     */
    Route::get('balance','index/Balance/index');
    Route::get('balance/account','index/Balance/account');
    Route::rule('account/add','index/Balance/addAccount','GET|POST');
    Route::get('balance/settle','index/Balance/settle');
    Route::get('balance/paid','index/Balance/paid');
    Route::rule('balance/apply','index/Balance/apply','GET|POST');
    /**
     * 订单
     */
    Route::get('order','index/Order/index');
    Route::get('order/refund','index/Order/refund');
    Route::get('order/submit','index/Order/submit');
    /**
     * API
     */
    Route::rule('api','index/Api/index','GET|POST');
    Route::get('api/channel','index/Api/channel');
    Route::get('api/doc','index/Api/document');

    /**
     * 登录注册
     */
    Route::rule('login','index/Login/login');  //商户登陆
    Route::rule('register','index/Login/register'); //商户注册
    Route::rule('logout','index/Login/logout'); //商户注册
    Route::post('validate/can-use-user','index/Login/checkUser');
    Route::post('validate/can-use-phone','index/Login/checkPhone');
    Route::post('validate/sms','index/Login/sendSmsCode');
    Route::rule('active/sendActive','index/Login/sendActiveCode');
    Route::get('active/:code','index/Login/checkActiveCode');
    //极验
    Route::get('validate/gt-start','index/Login/startGeetest');
    Route::post('validate/gt-verify','index/Login/checkGeetest');


});