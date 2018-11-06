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


class Bank extends BaseLogic
{

    /**
     * 获取所有支持银行
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getBankerList($where = [], $field = true, $order = 'create_time desc',$paginate = 15){
        return $this->modelBank->getList($where,$field, $order, $paginate);
    }
    /**
     * 所有支持银行总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @return mixed
     */
    public function getBankerCount($where = []){
        return $this->modelBank->getCount($where);
    }



    /**
     * 获取所有支持银行
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @return mixed
     */
    public function getBankerInfo($where = [], $field = true){
        return $this->modelBank->getInfo($where,$field);
    }

    public function saveBankerInfo(){

    }
}