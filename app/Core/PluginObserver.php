<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 23:21
 */

namespace App\Core;



interface PluginObserver
{

    public function message(CoolQ $coolQ);

    //notice
    public function event(CoolQ $coolQ);

    public function request(CoolQ $coolQ);

    public function other(CoolQ $coolQ);

}