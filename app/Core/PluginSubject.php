<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 23:20
 */

namespace App\Core;


interface PluginSubject
{
    // 添加/注册观察者
    public function attach(BasePlugin $plugin);

    // 删除观察者
    public function detach(BasePlugin $plugin);

    // 触发通知
    public function notify();
}