<?php
/**
 *                   _oo8oo_
 *                  o8888888o
 *                  88" . "88
 *                  (| -_- |)
 *                  0\  =  /0
 *                ___/'==='\___
 *              .' \\|     |// '.
 *             / \\|||  :  |||// \
 *            / _||||| -:- |||||_ \
 *            |   | \\\  -  /// |   |
 *            | \_|  ''\---/''  |_/ |
 *           \  .-\__  '-'  __/-.  /
 *         ___'. .'  /--.--\  '. .'___
 *       ."" '<  '.___\_<|>_/___.'  >' "".
 *     | | :  `- \`.:`\ _ /`:.`/ -`  : | |
 *     \  \ `-.   \_ __\ /__ _/   .-` /  /
 *  =====`-.____`.___ \_____/ ___.`____.-`=====
 *                   `=---=`
 *            佛祖保佑         永无bug
 *            Created by PhpStorm.
 *               User: kilingzhang
 *               Date: 18-3-23
 *               Time: 下午4:00
 */

namespace App\Core;


use App\Support\Log;
use App\Support\Time;

abstract class BasePlugin implements PluginObserver
{
    public $Intercept;
    private $startTime;
    public $coolQ;
    protected $messageOrders = [];
    protected $eventOrders = [];
    protected $requestOrders = [];
    protected $otherOrders = [];

    abstract public function getPluginName();

    public function __construct()
    {
        $this->Intercept = false;

        if (isset($this->coolQ)) {
            $this->coolQ->block = $this->Intercept;
        }

        $this->initOrders();
    }

    public abstract function initOrders();

    public function __destruct()
    {

    }

    public function onMessage(CoolQ $coolQ)
    {
        $this->coolQ = $coolQ;
        $this->startTime = Time::getMicrotime();
        $resopnse = $this->message($this->coolQ->getContent());
        $times = Time::ComMicritime($this->startTime, Time::getMicrotime());
        Log::debug($this->getPluginName() . '->' . __FUNCTION__ . ' 共耗时：' . $times . '秒', [$times]);
        return $resopnse;
    }

    public function onEvent(CoolQ $coolQ)
    {
        $this->coolQ = $coolQ;
        $this->startTime = Time::getMicrotime();
        $resopnse = $this->event($this->coolQ->getContent());
        $times = Time::ComMicritime($this->startTime, Time::getMicrotime());
        Log::debug($this->getPluginName() . '->' . __FUNCTION__ . ' 共耗时：' . $times . '秒', [$times]);
        return $resopnse;
    }

    public function onRequest(CoolQ $coolQ)
    {
        $this->coolQ = $coolQ;
        $this->startTime = Time::getMicrotime();
        $resopnse = $this->request($this->coolQ->getContent());
        $times = Time::ComMicritime($this->startTime, Time::getMicrotime());
        Log::debug($this->getPluginName() . '->' . __FUNCTION__ . ' 共耗时：' . $times . '秒', [$times]);
        return $resopnse;
    }

    public function onOther(CoolQ $coolQ)
    {
        $this->coolQ = $coolQ;
        $this->startTime = Time::getMicrotime();
        $resopnse = $this->other($this->coolQ->getContent());
        $times = Time::ComMicritime($this->startTime, Time::getMicrotime());
        Log::debug($this->getPluginName() . '->' . __FUNCTION__ . ' 共耗时：' . $times . '秒', [$times]);
        return $resopnse;
    }

    /**
     * @param bool $bool
     */
    public function setIntercept($bool = true)
    {
        $this->Intercept = $bool;

        if (isset($this->coolQ)) {
            $this->coolQ->block = $this->Intercept;
        }
    }

    /**
     * @return bool
     */
    public function isIntercept()
    {
        return $this->Intercept;
    }

    public function attach(string $type, BaseOrder $order)
    {
        switch ($type) {
            case 'message':
                $key = array_search($order, $this->messageOrders);
                if ($key === false) {
                    $this->messageOrders[] = $order;
                }
                break;
            case 'event':
                $key = array_search($order, $this->eventOrders);
                if ($key === false) {
                    $this->eventOrders[] = $order;
                }
                break;

            case 'request':
                $key = array_search($order, $this->requestOrders);
                if ($key === false) {
                    $this->requestOrders[] = $order;
                }
                break;

            case 'ohter':
                $key = array_search($order, $this->otherOrders);
                if ($key === false) {
                    $this->otherOrders[] = $order;
                }
                break;
        }
        Log::debug($order->getOrderName() . '  命令已加载');
    }

    public function detach(string $type, BaseOrder $order)
    {
        switch ($type) {
            case 'message':
                $key = array_search($order, $this->messageOrders);
                if ($key !== false) {
                    unset($this->messageOrders[$key]);
                }
                break;
            case 'event':
                $key = array_search($order, $this->eventOrders);
                if ($key !== false) {
                    unset($this->eventOrders[$key]);
                }
                break;

            case 'request':
                $key = array_search($order, $this->requestOrders);
                if ($key !== false) {
                    unset($this->requestOrders[$key]);
                }
                break;

            case 'ohter':
                $key = array_search($order, $this->otherOrders);
                if ($key !== false) {
                    unset($this->otherOrders[$key]);
                }
                break;
        }
        Log::debug($order->getOrderName() . '  命令已卸载');
    }

    public function runMessageOrders()
    {
        foreach ($this->messageOrders as $order) {
            $order->run($this->coolQ, $this->coolQ->getContent());
        }
    }

    public function runEventOrders()
    {
        foreach ($this->eventOrders as $order) {
            $order->run($this->coolQ, $this->coolQ->getContent());
        }
    }

    public function runRequestOrders()
    {
        foreach ($this->requestOrders as $order) {
            $order->run($this->coolQ, $this->coolQ->getContent());
        }
    }

    public function runOtherOrders()
    {
        foreach ($this->otherOrders as $order) {
            $order->run($this->coolQ, $this->coolQ->getContent());
        }
    }

    public function message(array $content)
    {
        $this->runMessageOrders();
    }

    public function event(array $content)
    {
        $this->runEventOrders();
    }

    public function request(array $content)
    {
        $this->runRequestOrders();
    }

    public function other(array $content)
    {
        $this->runOtherOrders();
    }

}