<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/13
 * Time: 0:08
 */

namespace App\Support;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log

{
    private static $loggerInstance = null;
    private static $loggerPath = null;
    private static $loggerName = null;
    private static $loggerId = null;

    private function __construct()
    {
        // 注意 日志记录 只会记录高于设置的等级  debug->EMERGENCY 由小到大
        $logger = new Logger(self::$loggerName);
        $level = getenv('LOG_LEVEL', 'INFO');
        switch ($level) {
            case 'DEBUG':
                $level = Logger::DEBUG;
                break;
            case 'INFO':
                $level = Logger::INFO;
                break;
            case 'NOTICE':
                $level = Logger::NOTICE;
                break;
            case 'WARNING':
                $level = Logger::WARNING;
                break;
            case 'ERROR':
                $level = Logger::ERROR;
                break;
            case 'CRITICAL':
                $level = Logger::CRITICAL;
                break;
            case 'ALERT':
                $level = Logger::ALERT;
                break;
            case 'EMERGENCY':
                $level = Logger::EMERGENCY;
                break;
            default:
                $level = Logger::INFO;
                break;
        }
        try {
            $logger->pushHandler(new StreamHandler(self::$loggerPath, $level));
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
        }
        self::$loggerInstance = $logger;
    }

    public static function getInstance($name)
    {
        if (self::$loggerInstance === null) {
            if (self::$loggerName === null) {
                throw  new \Exception('logger name must be not empty');
            }
            if (self::$loggerPath === null) {
                throw  new \Exception('logger path must be not empty');
            }
            new Log(self::$loggerName);
        }

        if (self::$loggerId == null) {
            self::$loggerId = Time::getMicrotime();
        }

        return self::$loggerInstance;
    }

    /**
     * @return null
     */
    public static function getLoggerPath()
    {
        return self::$loggerPath;
    }

    /**
     * @return null
     */
    public static function getLoggerName()
    {
        return self::$loggerName;
    }

    /**
     * @param null $loggerPath
     */
    public static function setLoggerPath($loggerPath)
    {
        if (self::$loggerId == null) {
            self::$loggerId = Time::getMicrotime();
        }
        self::$loggerPath = $loggerPath;
    }

    /**
     * @param null $loggerName
     */
    public static function setLoggerName($loggerName)
    {
        if (self::$loggerId == null) {
            self::$loggerId = Time::getMicrotime();
        }
        self::$loggerName = $loggerName;
    }

    public static function emergency($message, $content = array())//紧急状况，比如系统挂掉
    {
        $msg = self::$loggerId . ' | ';
        try {
            return self::getInstance(self::$loggerName)->emergency($msg . $message, $content);
        } catch (\Exception $e) {
        }
    }

    public static function alert($message, $content = array())//需要立即采取行动的问题，比如整站宕掉，数据库异常等，这种状况应该通过短信提醒
    {
        $msg = self::$loggerId . ' | ';
        try {
            return self::getInstance(self::$loggerName)->alert($msg . $message, $content);
        } catch (\Exception $e) {
        }
    }

    public static function critical($message, $content = array()) //严重问题，比如：应用组件无效，意料之外的异常
    {
        $msg = self::$loggerId . ' | ';
        try {
            return self::getInstance(self::$loggerName)->critical($msg . $message, $content);
        } catch (\Exception $e) {
        }
    }

    public static function warning($message, $content = array())//警告但不是错误，比如使用了被废弃的API
    {
        $msg = self::$loggerId . ' | ';
        try {
            return self::getInstance(self::$loggerName)->warning($msg . $message, $content);
        } catch (\Exception $e) {
        }
    }

    public static function notice($message, $content = array())//普通但值得注意的事件
    {
        $msg = self::$loggerId . ' | ';
        try {
            return self::getInstance(self::$loggerName)->notice($msg . $message, $content);
        } catch (\Exception $e) {
        }
    }

    public static function info($message, $content = array())//感兴趣的事件，比如登录、退出
    {
        $msg = self::$loggerId . ' | ';
        try {
            return self::getInstance(self::$loggerName)->info($msg . $message, $content);
        } catch (\Exception $e) {
        }
    }

    public static function debug($message, $content = array())//详细的调试信息
    {
        $msg = self::$loggerId . ' | ';
        try {
            return self::getInstance(self::$loggerName)->debug($msg . $message, $content);
        } catch (\Exception $e) {

        }
    }

    public static function error($message, $content = array())//运行时错误，不需要立即处理但需要被记录和监控
    {
        $msg = self::$loggerId . ' | ';
        try {
            return self::getInstance(self::$loggerName)->error($msg . $message, $content);
        } catch (\Exception $e) {
        }

    }


}