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

// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\install\controller;

use app\common\library\enum\CodeEnum;
use app\install\logic\Install;
use think\Controller;

/**
 * 安装控制器
 */
class Index extends Controller
{

    /**
     * Index constructor.
     */
    public function __construct()
    {
        // 执行父类构造方法
        parent::__construct();
        
        'complete' != $this->request->action() && $this->checkInstall();
    }

    /**
     * 检查是否已安装
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function checkInstall()
    {
        file_exists(DATA_PATH . 'install.lock') && $this->error('已经成功安装，请勿重复安装!','/');
    }

    /**
     * 安装引导首页
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index()
    {
        !function_exists('saeAutoLoader') && $dirfile = check_dirfile();

        $this->assign('dirfile', $dirfile);

        $this->assign('env', check_env());

        $this->assign('func', check_func());

        return $this->fetch();
    }
    
    /**
     * 安装成功页
     */
    public function complete()
    {

        return $this->fetch('complete');
    }

    /**
     * 安装数据写入
     */
    public function step1($db = null, $admin = null)
    {

        if ($this->request->isPost()) {

            $install = new Install();
            // 检查安装数据
            $install->check($db, $admin);

            // 开始安装
            return $install->install($db, $admin);

        }
        return $this->fetch();
    }
}
