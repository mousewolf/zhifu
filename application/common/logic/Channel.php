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
use think\Db;
use think\Log;

class Channel extends BaseLogic
{

    /**
     * 获取渠道列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param $field
     * @param string $order
     * @return mixed
     */
    public function getChannelAll($where = [], $field = true, $order = 'create_time desc',$paginate = 15){
        $data = $this->modelChannel->getList($where,$field, $order, $paginate);
        return $data ? [CodeEnum::SUCCESS,'',$data] : [CodeEnum::ERROR,'',$data];
    }

    /**
     * 获取渠道配置
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $id
     * @return mixed
     */
    public function getChannelParam($id){
        return [ CodeEnum::SUCCESS , $this->modelChannel->getValue(['id'=>$id],'param')];
    }

    /**
     * 获取渠道信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @return mixed
     */
    public function getChannelInfo($where = [], $field = true)
    {
        return $this->modelChannel->getInfo($where, $field);
    }

    /**
     * 添加一个渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @return array|string
     */
    public function addChannel($data){
        //TODO 数据验证
        $validate = $this->validateChannel->check($data);

        if (!$validate) {
            return $this->validateChannel->getError();
        }
        //TODO 添加数据
        Db::startTrans();
        try{
            $this->modelChannel->setInfo($data);
            Db::commit();
            return [ CodeEnum::SUCCESS,'添加渠道成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [ CodeEnum::ERROR,'未知错误'];
        }

    }

    /**
     * 编辑渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @return array|string
     */
    public function editChannel($data){

        //TODO 数据验证
        $validate = $this->validateChannel->check($data);

        if (!$validate) {
            return $this->validateChannel->getError();
        }
        //TODO 添加数据
        Db::startTrans();
        try{
            $this->modelChannel->setInfo($data);
            Db::commit();
            return [ CodeEnum::SUCCESS,'渠道修改成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [ CodeEnum::ERROR,'未知错误'];
        }
    }

    /**
     * 改变渠道可用性
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @param int $value
     * @return array
     */
    public function setChannelStatus($where,$value = 0){
        Db::startTrans();
        try{
            $this->modelChannel->setFieldValue($where, $field = 'status', $value);
            Db::commit();
            return [ CodeEnum::SUCCESS,'修改状态成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [ CodeEnum::ERROR,'未知错误'];
        }
    }

    /**
     * 删除一个渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @return array
     */
    public function delChannel($where){
        Db::startTrans();
        try{
            $this->modelChannel->deleteInfo($where);
            Db::commit();
            return [ CodeEnum::SUCCESS,'删除渠道成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [ CodeEnum::ERROR,'未知错误'];
        }
    }
}