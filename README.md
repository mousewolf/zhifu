Caomao_2018_Beta_v1.0
===============

一键聚合支付系统，支持第三/四方代码接入，一个支付API接入，轻松实现收款。
目前整合包括：

 + 微信
  + 微信扫码
 + 支付宝
  + 支付宝web
   + 支付宝wap
    + 支付宝扫码
 + QQ
  + QQ扫码
 + 更多等待...

> 运行环境要求PHP > 5.6以上(推荐7.0.*)。

使用系统前请：
>详细开发文档参考 [ThinkPHP5完全开发手册](http://www.kancloud.cn/manual/thinkphp5)

## 目录结构

目录结构如下：

~~~
部署目录（或者子目录）
├─application           应用目录
│  ├─common             公共模块目录（可以更改）
│  ├─admin              运营模块目录
│  │  ├─config.php      模块配置文件
│  │  ├─common.php      模块函数文件
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  ├─view            视图目录
│  │  └─ ...            更多类库目录
│  ├─api                API模块目录
│  │  ├─config.php      模块配置文件
│  │  ├─common.php      模块函数文件
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  ├─view            视图目录
│  │  └─ ...            更多类库目录
│  ├─index              商户模块目录
│  │  ├─config.php      模块配置文件
│  │  ├─common.php      模块函数文件
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  ├─view            视图目录
│  │  └─ ...            更多类库目录
│  │
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─database.php       数据库配置文件
│
├─public                WEB目录（对外访问目录）
│  ├─status             基本静态文件
│  │  ├─admin           
│  │  ├─index
│  │  └─ ...          
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重写
│
├─data                  应用数据存放目录
│  ├─cret               商户证书目录         
│  ├─cront              定时器脚本目录
│  ├─extend             拓展类库目录
│  ├─runtime            运行日志目录
│  ├─supervisord        守护进程日志目录
|  └─ ...
├─thinkphp              ThinkPHP框架系统目录
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
~~~

## **问题反馈**

在使用中有任何问题，请使用以下联系方式联系我们

QQ群: [939417065](交流群 暗号：Caomao)

Email: (brianwaring98#gmail.com, 把#换成@)

Github: https://github.com/iredcap

## **特别鸣谢**

感谢以下的项目,排名不分先后

ThinkPHP：http://www.thinkphp.cn

LayuiAdmin：https://www.layui.com/admin/ (商用请授权)

OneBase： https://www.onebase.org


## **版权信息**

Caomao遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2017-2018 by Iredcap (https://www.iredcap.cn)

All rights reserved。
