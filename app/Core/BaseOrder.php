<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/25
 * Time: 23:59
 */

namespace App\Core;


use Kilingzhang\QQ\Core\QQ;

abstract class BaseOrder
{
    public abstract function getOrderName();

    public abstract function run(QQ $QQ, array $content);
}