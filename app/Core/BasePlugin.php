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
use Kilingzhang\QQ\Core\QQ;

abstract class BasePlugin implements PluginObserver
{
    protected $messageOrders = [];
    protected $eventOrders = [];
    protected $requestOrders = [];
    protected $otherOrders = [];

    /**
     * @var QQ
     */
    protected $QQ = null;

    abstract public function getPluginName();

    public function __construct()
    {
        $this->initOrders();
    }

    public abstract function initOrders();

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

    public function QQ()
    {
        return $this->QQ;
    }

    public function onMessage(QQ $QQ)
    {
        $this->QQ = $QQ;
        $content = $this->QQ()->getContent();
        $this->message($content);
    }

    public function onNotice(QQ $QQ)
    {
        $this->QQ = $QQ;
        $content = $this->QQ()->getContent();
        $this->notice($content);
    }

    public function onRequest(QQ $QQ)
    {
        $this->QQ = $QQ;
        $content = $this->QQ()->getContent();
        $this->request($content);
    }

    public function onOther(QQ $QQ)
    {
        $this->QQ = $QQ;
        $content = $this->QQ()->getContent();
        $this->other($content);
    }

    public function message(array $content)
    {
        foreach ($this->messageOrders as $order) {
            $order->run($this->QQ(), $content);
        }
    }

    public function notice(array $content)
    {
        foreach ($this->eventOrders as $order) {
            $order->run($this->QQ(), $content);
        }
    }

    public function request(array $content)
    {
        foreach ($this->requestOrders as $order) {
            $order->run($this->QQ(), $content);
        }
    }

    public function other(array $content)
    {
        foreach ($this->otherOrders as $order) {
            $order->run($this->QQ(), $content);
        }
    }


}