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


    private $client = null;


    public function __construct()
    {
        //MUST  必须复用父类析构函数
        parent::__construct();
        $this->client = new Client();
    }

    public function __destruct()
    {
        //MUST  如果需要复写 必须调用父类
        parent::__destruct();
    }


    public function getPluginName()
    {
        return 'MusicPlugin';
    }

    public function message(array $content)
    {

        switch ($content['message_type']) {
            //私聊消息
            case "private":


                break;
            //群消息
            case "group":


                break;
            //讨论组消息
            case "discuss":


                break;

        }

    }

    public function event(array $content)
    {
    }

    public function request(array $content)
    {
    }

    public function other(array $content)
    {
    }

}