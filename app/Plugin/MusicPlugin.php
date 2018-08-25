<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 22:19
 */

namespace App\Plugin;


use App\Core\BasePlugin;
use App\Core\CoolQ;
use CoolQSDK\CQ;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class MusicPlugin extends BasePlugin
{

    public function getPluginName()
    {
        return 'MusicPlugin';
    }

    public function initOrders()
    {
    }
}