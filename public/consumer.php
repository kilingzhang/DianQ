<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/21
 * Time: 22:47
 */

use Beanstalk\Client;

require_once __DIR__ . '/../vendor/autoload.php';
$b = new Pheanstalk\Pheanstalk('115.159.211.21', 11300);

$b->useTube('firstTube');

$b->put('hello world . ' . time());

$b->watch('firstTube');
$b->ignore('default');

//如果无任务，这里会阻塞
$job = $b->reserve();

echo $job->getData() . "\n";

$b->delete($job);