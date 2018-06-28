<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/17
 * Time: 2:02
 */

use App\Support\Log;
use App\Support\Time;
use Dotenv\Dotenv;

include __DIR__ . '/../vendor/autoload.php';


$dotenv = new Dotenv(__DIR__ . '/../');

$dotenv->load();

define('COOLQ_START', \App\Support\Time::getMicrotime());
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
define('LOG_PATH', ROOT_PATH . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . getenv('APP_NAME') . '.log');




$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->run();