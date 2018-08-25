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
use App\Order\TulingOrder;
use App\Support\Log;
use App\Support\Time;
use CoolQSDK\CQ;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class TulingPlugin extends BasePlugin
{

    public function getPluginName()
    {
        return 'TulingPlugin';
    }


    public function initOrders()
    {
        $this->attach('message', new TulingOrder());
    }
}