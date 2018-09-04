<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 22:19
 */

namespace App\Plugins;


use App\Core\BasePlugin;

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