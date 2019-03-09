<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-8
 * Time: 上午10:21
 */
// [ 应用入口文件 ]

ini_set("display_errors","On");
error_reporting(E_ALL);
//检测安装
if(!file_exists(__DIR__ . '/data/install.lock')){
    // 绑定安装模块
    define('BIND_MODULE', 'install');
}

// 定义项目路径
define('APP_PATH', __DIR__ . '/application/');
// 定义上传路径
define('UPLOAD_PATH', __DIR__ . '/uploads/');
// 定义数据目录
define('DATA_PATH', __DIR__ . '/data/');

// 定义配置目录
define('CONF_PATH', DATA_PATH . 'conf/');
// 定义证书目录
define('CRET_PATH', DATA_PATH . 'cret/');
// 定义EXTEND目录
define('EXTEND_PATH', DATA_PATH . 'extend/');
// 定义RUNTIME目录
define('RUNTIME_PATH', DATA_PATH . 'runtime/');

// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
