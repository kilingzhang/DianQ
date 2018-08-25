<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 22:19
 */

namespace App\Plugin;


use App\Core\BasePlugin;
use App\Order\GroupIncreaseNoticeOrder;

class GongGongPlugin extends BasePlugin
{

    public function getPluginName()
    {
        return 'GongGongPlugin';
    }

    public function initOrders()
    {
        $this->attach('event',new GroupIncreaseNoticeOrder());
    }
}