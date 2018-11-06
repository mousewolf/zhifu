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
     * @param string $action 动作
     * @param string $describe
     * @return array
     */
    public function logAdd($action = '', $describe = '')
    {

        $request = request();
        $module = $request->module();

        $uid = session('admin_info')['id'] ?:session('user_info')['uid'];

        $data['uid']       = $uid;
        $data['module']    = $module;
        $data['ip']        = $request->ip();
        $data['url']       = $request->url();
        $data['action']    = $action;
        $data['describe']  = $action . $describe;

        $res = $this->modelActionLog->setInfo($data);

        return $res || !empty($res) ? ['code' => CodeEnum::SUCCESS, 'msg' =>'日志添加成功', '']
            : ['code' => CodeEnum::ERROR, 'msg' => '加入操作日志失败'];
    }

    /**
     * 获取日志总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @return mixed
     */
    public function getLogCount($where = []){
        return $this->modelActionLog->getCount($where);
    }

    /**
     * 获取日志列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @return mixed
     */
    public function getLogList($where = []){
        $this->modelActionLog->limit = true;
        return $this->modelActionLog->getList($where, true, 'create_time desc',false);
    }

    /**
     *
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @return array
     */
    public function logDel($where = [])
    {

        return $this->modelActionLog->deleteInfo($where) ? ['code' => CodeEnum::SUCCESS, 'msg' =>'日志删除成功', '']
            : ['code' => CodeEnum::ERROR, 'msg' => '删除操作日志失败'];
    }
}