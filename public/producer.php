<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/21
 * Time: 22:47
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Beanstalk\Client;

//
// A sample producer.
//
function producer()
{
    $this->beanstalkd->useTube('default');
    $n = 1;
    while ($n) {
        $delay = mt_rand(0, 30);
        $this->beanstalkd->put(
            2, // priority.
            $delay,  //  delay. 秒数
            3, // run time
            "beanstalkd $n delay $delay" // The job's body.
        );
        $n--;
    }
}

producer();