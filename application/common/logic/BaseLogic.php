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
use app\common\model\BaseModel;
use app\common\service\Code;
use think\cache\driver\Redis;
use think\Log;

class BaseLogic extends BaseModel
{

    /**
     * 当日API访问次数限制
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $key
     * @return false|int
     */
    public function checkFrequent($key= ''){
        //API 访问限制
        $name = !empty($key) ? $key : 'client-ip:' . request()->ip();
        $redis = new Redis();
        $value = $redis->get($name);
        //没有数据
        if (!$value) {
            // 写入ip
            $redis->set($name, 0, 36000);
        }
        //一天内 次数超过 10 次 停止本次请求
        if ($value >= 10) {
            Log::error("Trigger restriction and flow control");
            return false;
        }
        //正常范围跟 自增一次
        return $redis->inc($name);
    }

    /**
     * 发送验证码
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $whom
     * @param string $drive
     * @return array
     * @throws \think\Exception
     */
    public function sendCode($whom,$drive = ''){
        //请求限制
        if (!$this->checkFrequent($whom)){
            return [ CodeEnum::ERROR, "发送失败，验证码接口请求限制 10次天"];
        };
        //邮件附加参数
        $param = [
          'subject' => '您本次操作验证码',
        ];
        //1.初始化验证码驱动
        if (!empty($drive)){
            Code::init($drive);
        }
        //2.发送验证码
        $res = Code::send($whom, $param);
        return $res ? [ CodeEnum::SUCCESS, "发送成功"]:[ CodeEnum::ERROR, "发送失败"];
    }

    /**
     * 校验验证码
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $whom
     * @param string $code
     * @return mixed
     * @throws \think\Exception
     */
    public function vaildCode($whom,$code = ''){
        //3.对比验证码
        return Code::valid($whom,$code);
    }

}