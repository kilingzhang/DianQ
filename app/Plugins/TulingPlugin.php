<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 22:19
 */

namespace App\Plugins;


use App\Core\BasePlugin;
use App\Orders\TulingOrder;

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