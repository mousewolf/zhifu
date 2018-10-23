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
namespace app\api\logic;
use app\common\library\exception\ParameterException;
use app\common\library\exception\OrderException;
use app\common\library\exception\UserException;

/**
 * 下单处理
 *
 * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
 *
 */
class PrePay extends BaseApi
{
    /**
     *
     * 1.构建支付订单
     * 2.请求支付对象并返回商户
     * 3.用户扫码完成支付
     * 4.订单队列处理异步回调
     * 5.完成订单
     *
     * 构建支付订单
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $orderData
     * @return mixed
     * @throws ParameterException
     */
    public function orderPay($orderData){
        // 空值控制
        if(!is_null($orderData)){

            //等所有基本数据检查完成后  对订单进行检查数据
            $this->validateUnifiedOrder->goCheck();

            //TODO 订单持久化（估计用到队列）并提交支付
            return $this->logicOrders->createPayOrder($orderData);

        }
        throw new ParameterException([
            'msg'   => 'Create Order Error:[Order Fail].'
        ]);
    }

    /**
     * 查询订单
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $queryData
     */
    public function orderQuery($queryData){
        // 验证
        $this->validateQueryorder->gocheck();
    }
}