<?php
/**
 * +---------------------------------------------------------------------+
 * | Yubei         | [ WE CAN DO IT JUST THINK ]
 * +---------------------------------------------------------------------+
 * | Licensed    | http://www.apache.org/licenses/LICENSE-2.0 )
 * +---------------------------------------------------------------------+
 * | Author       | Brian Waring <BrianWaring98@gmail.com>
 * +---------------------------------------------------------------------+
 * | Company   | 小红帽科技      <Iredcap. Inc.>
 * +---------------------------------------------------------------------+
 * | Repository | https://github.com/BrianWaring/Yubei
 * +---------------------------------------------------------------------+
 */

namespace Iredcap\Pay;

use Iredcap\Pay\Util\PayObject;

class Charge extends PayObject
{

    /**
     * 创建支付
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param null $params
     * @return mixed
     * @throws exception\AuthorizationException
     * @throws exception\Exception
     * @throws exception\InvalidRequestException
     */
    public static function create($params = null)
    {

        return self::_request(Pay::$baseUrl,$params);
    }

    /**
     * 支付回调
     *
     * @param $chargeId
     * @return mixed|string
     * @throws Exception\Exception
     * @throws exception\AuthorizationException
     * @throws exception\InvalidRequestException
     */
    public static function retrieve($chargeId)
    {
        if (empty($chargeId)){
            return 'chargeId can not be blank.';
        }
        return self::_request(Pay::$baseUrl.'/'.$chargeId);
    }
}
