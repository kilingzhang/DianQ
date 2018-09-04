<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 1:25
 */

namespace App\Orders;


use App\Core\BaseOrder;
use CoolQSDK\CoolQ;

class RequestOrder extends BaseOrder
{

    public function getOrderName()
    {
    }

    public function run(CoolQ $coolQ, array $content)
    {
    }
}