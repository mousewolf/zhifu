<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-11
 * Time: 上午10:48
 */

namespace app\ownpay\logic;

use app\common\logic\BaseLogic;
use app\common\library\enum\CodeEnum;
use think\Db;
use think\Log;
use think\Validate;



class Order extends Base
{
    public function getOrderList($where = [], $field = true, $order = '', $paginate = 0)
    {

    }
}