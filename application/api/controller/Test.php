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

namespace app\api\controller;

use think\Log;
use think\Queue;
use think\exception\HttpResponseException;
use think\Response;

class Test extends BaseApi
{

    public function actionNotify(){
        $this->logicBalanceChange->creatBalanceChange(8888,66666);

        $data = request()->param();
        Log::notice('Test Order Header:' . json_encode(request()->header()));
        Log::notice('Test Order Notify:' . json_encode(request()->param()));

        http_response_code(200);    //设置返回头部

        $return['result_code'] = 'OK';
        $return['result_msg'] = 'SUCCESS';
        $return['data'] = $data;

        //签名及数据返回
        $response = Response::create($return,'json');
        // 抛数据
        throw new HttpResponseException($response);
    }

}