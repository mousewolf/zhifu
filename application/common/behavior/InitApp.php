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

namespace app\common\behavior;

use think\Request;

/**
 * 应用初始化基础信息行为
 *
 * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
 *
 */
class InitApp
{

    /**
     * 初始化行为入口
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function run()
    {
        // 初始化分层名称常量
        $this->initLayerConst();

        $this->initSystemConf();
    }

    /**
     * 初始化分层名称常量
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    private function initLayerConst()
    {

        define('LOGIC_LAYER_NAME'       , 'logic');
        define('MODEL_LAYER_NAME'       , 'model');
        define('SERVICE_LAYER_NAME'     , 'service');
        define('CONTROLLER_LAYER_NAME'  , 'controller');
        define('LIBRARY_LAYER_NAME'     , 'library');
        define('VALIDATE_LAYER_NAME'    , 'validate');
        define('VIEW_LAYER_NAME'        , 'view');

    }

    /**
     * 初始化配置信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     */
    private function initSystemConf()
    {

        $model = model('app\common\model\Config');

        $config_list = auto_cache('config_list', create_closure($model, 'all'));

        foreach ($config_list as $info) {

            $config_array[$info['name']] = $info['value'];
        }
        //写入配置
        config('site' ,$config_array);

    }

}
