<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/25
 * Time: 23:45
 */

namespace App\Support;


use CoolQSDK\CQ;

class Msg
{
    public static function isAt($message)
    {
        preg_match_all('/\[CQ:at,qq=(\d+)\]/', $message, $matches);
        return empty($matches[1][0]) == false;
    }

    public static function findAt($message)
    {
        preg_match_all('/\[CQ:at,qq=(\d+)\]/', $message, $matches);
        return empty($matches[1][0]) ? 0 : $matches[1][0];
    }


    public static function filterCQAt($message)
    {
        return CQ::filterCQAt($message);
    }

}
