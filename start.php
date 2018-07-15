<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/17
 * Time: 15:18
 */

use App\Core\CoolQ;
use Dotenv\Dotenv;

define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);

include ROOT_PATH . 'vendor/autoload.php';

$dotenv = new Dotenv(ROOT_PATH);

$dotenv->load();

define('LOG_PATH', ROOT_PATH . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . getenv('APP_NAME') . '.log');
define('COOLQ_START', \App\Support\Time::getMicrotime());


$app = require_once ROOT_PATH . 'bootstrap/app.php';


$app->run();


