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

    public function message(array $content);

    //notice
    public function notice(array $content);

    public function request(array $content);

    public function other(array $content);

}