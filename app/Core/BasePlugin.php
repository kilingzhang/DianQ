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
    public $coolQ = null;

    abstract public function getPluginName();

    public function __construct()
    {

        $this->Intercept = false;

        if (isset($this->coolQ)) {
            $this->coolQ->block = $this->Intercept;
        }


    }


    public function __destruct()
    {

    }


    public function onMessage(CoolQ $coolQ)
    {
        $this->coolQ = $coolQ;
        $this->startTime = Time::getMicrotime();
        $resopnse = $this->message($coolQ);
        $times = Time::ComMicritime($this->startTime, Time::getMicrotime());
        Log::debug($this->getPluginName() . '->' . __FUNCTION__ . ' 共耗时：' . $times . '秒' , [$times]);
        return $resopnse;
    }

    public function onEvent(CoolQ $coolQ)
    {
        $this->coolQ = $coolQ;
        $this->startTime = Time::getMicrotime();
        $resopnse = $this->event($coolQ);
        $times = Time::ComMicritime($this->startTime, Time::getMicrotime());
        Log::debug($this->getPluginName() . '->' . __FUNCTION__ . ' 共耗时：' . $times . '秒' , [$times]);
        return $resopnse;
    }

    public function onRequest(CoolQ $coolQ)
    {
        $this->coolQ = $coolQ;
        $this->startTime = Time::getMicrotime();
        $resopnse = $this->request($coolQ);
        $times = Time::ComMicritime($this->startTime, Time::getMicrotime());
        Log::debug($this->getPluginName() . '->' . __FUNCTION__ . ' 共耗时：' . $times . '秒' , [$times]);
        return $resopnse;
    }

    public function onOther(CoolQ $coolQ)
    {
        $this->coolQ = $coolQ;
        $this->startTime = Time::getMicrotime();
        $resopnse = $this->other($coolQ);
        $times = Time::ComMicritime($this->startTime, Time::getMicrotime());
        Log::debug($this->getPluginName() . '->' . __FUNCTION__ . ' 共耗时：' . $times . '秒' , [$times]);
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


}