<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/17
 * Time: 2:17
 */

namespace App\Core;


use App\Support\Log;
use App\Support\Time;

class CoolQ extends \CoolQSDK\CoolQ implements PluginSubject
{

    private static $plugins = array();
    private $crulStartTime = '';
    public $block = false;

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
            if ($this->isUseWs()) {
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
        $this->block = false;

    }

    private function onListener($plugin)
    {
        if (!$this->block) {
            Log::debug('已通知插件 ' . $plugin->getPluginName() . ' ', $this->getPutParams());
            switch ($this->getPostType()) {
                //收到消息
                case 'message':
                    $plugin->onMessage($this);
                    break;
                //群、讨论组变动等非消息类事件
                //兼容4.x
                case 'notice':
                case 'event':
                    $plugin->onEvent($this);
                    break;
                //加好友请求、加群请求／邀请
                case 'request':
                    $plugin->onRequest($this);
                    break;
                default:
                    $plugin->onOther($this);
                    break;
            }
        }

        if ($this->block == true) {
            Log::debug($plugin->getPluginName() . '插件已拦截后续插件', [$this->block]);
        }
    }

    public function run()
    {
        $this->event();
        //框架耗时日志记录
        $times = Time::ComMicritime(COOLQ_START, Time::getMicrotime());
        Log::debug('框架总耗时：' . $times . '秒', [$times]);
        if (getenv('APP_DEBUG') == true && getenv('LOG_LEVEL') != 'DEBUG') {
            Log::info('框架总耗时：' . $times . '秒', [$times]);
        }
    }

    public function beforeCurl($uri = '', $param = [])
    {
        $this->crulStartTime = Time::getMicrotime();
    }

    public function afterCurl($uri = '', $param = [], $response, $errorException)
    {
        $times = Time::ComMicritime($this->crulStartTime, Time::getMicrotime());
        if ($errorException != null) {
            if ($errorException !== null) {
                Log::error($uri . ' 请求总耗时：' . $times . '秒', $errorException->getMessage());
            } else {
                Log::error($uri . ' 请求总耗时：' . $times . '秒');
            }
        } else {
            Log::debug($uri . ' 请求总耗时：' . $times . '秒', [
                'request' => $param,
                'response' => $response,
            ]);
        }
    }

    public function onSignature($isHMAC)
    {
        if (!$isHMAC) {
            $this->returnJsonApi(Response::signatureError());
        }
    }

    public function onMessage($content)
    {
        return $this->notify();
    }

    public function onEvent($content)
    {
        return $this->notify();
    }

    public function onRequest($content)
    {
        return $this->notify();
    }

    public function onOther($content)
    {
        return $this->notify();
    }

    public function beforEvent()
    {

    }

    public function afterEvent()
    {

    }


}