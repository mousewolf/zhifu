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

namespace app\common\logic;


use app\common\library\enum\CodeEnum;

class Log extends BaseLogic
{

    /**
     * 增加一个操作日志
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $name
     * @param string $describe
     * @return array
     */
    public function logAdd($name = '', $describe = '')
    {

        $request = request();


        $user = session('admin_info')['id'] ?:session('user_info')['uid'];

        $data['uid']       = $user;
        $data['ip']        = $request->ip();
        $data['url']       = $request->url();
        $data['name']      = $name;
        $data['describe']  = $describe;

        $res = $this->modelActionLog->setInfo($data);

        return $res || !empty($res) ? [CodeEnum::SUCCESS, '日志添加成功', ''] : [CodeEnum::ERROR, '加入操作日志失败'];
    }
}