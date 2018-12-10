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


namespace app\api\service\request;
use app\common\library\exception\ForbiddenException;
use think\Log;
use think\Request;

/**
 * 检验接口允许IP
 *
 * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
 *
 */
class CheckAllowed extends ApiCheck
{

    /**
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param Request $request
     * @return mixed|void
     * @throws ForbiddenException
     */
    public function doCheck(Request $request)
    {
        // 获取Ip Map
        $checkAllowedIpMap = (array)$this->logicApi->getallowedIpMap();

        //存在性
        if (!in_array($request->ip(), $checkAllowedIpMap)) {
            throw new ForbiddenException([
                'msg'=>'Invalid Request.[ Request IP not authorized.]',
                'errorCode'=> 400003
            ]);
        }

    }
}
