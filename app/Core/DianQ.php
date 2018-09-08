<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/9/8
 * Time: 16:36
 */

namespace App\Core;

use App\Support\Log;
use App\Support\Time;
use Kilingzhang\QQ\CoolQ\QQ;
use Kilingzhang\QQ\Core\Response;
use function Kilingzhang\QQ\http_put;
use function Kilingzhang\QQ\http_server;

class DianQ implements PluginSubject
{
    /**
     * @var QQ
     */
    private $QQ;
    private $host;
    private $token;
    private $secret;
    private static $plugins = array();

    public function __construct($host, $token, $secret)
    {
        $this->host = $host;
        $this->token = $token;
        $this->secret = $secret;
    }

    public function register($config)
    {
        try {
            $protocol = new $config['protocol']($this->host, $this->token, $this->secret);
            $this->QQ = new $config['driver']($protocol);
        } catch (\Exception $exception) {

        }

    }

    public function QQ(): QQ
    {
        return $this->QQ;
    }

    public function attach(BasePlugin $plugin)
    {
        $key = array_search($plugin, self::$plugins);
        if ($key === false) {
            self::$plugins[] = $plugin;
        }
        Log::debug($plugin->getPluginName() . '  插件已加载');
    }

    public function detach(BasePlugin $plugin)
    {
        $key = array_search($plugin, self::$plugins);
        if ($key !== false) {
            unset(self::$plugins[$key]);
        }
        Log::debug($plugin->getPluginName() . '  插件已注销');
    }

    public function notify()
    {
        $pids = array();
        foreach (self::$plugins as $key => $plugin) {
            $pids[$key] = 0;
            if ($this->QQ()->getProtocol()->isCli()) {
                $pids[$key] = pcntl_fork();
            }
            if ($pids[$key] == -1) {
                Log::error($plugin->getPluginName() . "fork thread failed!");
            } elseif ($pids[$key]) {
                pcntl_waitpid($pids[$key], $status);
                break;
            } else {
                $this->onListener($plugin);
            }
        }
    }

    public function run()
    {
        $content = [];
        $response = $this->QQ()->event(
            function ($content) {
                $this->notify();
            },
            function ($content) {
                $this->notify();
            },
            function ($content) {
                $this->notify();
            },
            function ($content) {
                $this->notify();
            });
        //框架耗时日志记录
        $times = Time::ComMicritime(COOLQ_START, Time::getMicrotime());
        Log::debug('框架总耗时：' . $times . '秒', [$times]);
        if (getenv('APP_DEBUG') == true && getenv('LOG_LEVEL') != 'DEBUG') {
            Log::info('框架总耗时：' . $times . '秒', [$times]);
        }
        $this->returnApi($response);
    }

    private function onListener(BasePlugin $plugin)
    {
        $content = $this->QQ()->getContent();
        Log::debug('已通知插件 ' . $plugin->getPluginName() . ' ', $content);
        switch ($content['post_type']) {
            //收到消息
            case 'message':
                $plugin->onMessage($this->QQ());
                break;
            //群、讨论组变动等非消息类事件
            //兼容4.x
            case 'notice':
            case 'event':
                $plugin->onNotice($this->QQ());
                break;
            //加好友请求、加群请求／邀请
            case 'request':
                $plugin->onRequest($this->QQ());
                break;
            default:
                $plugin->onOther($this->QQ());
                break;
        }
    }


    public function returnApi(Response $response)
    {
        $this->QQ()->returnApi($response);
    }
}