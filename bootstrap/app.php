<?php


use App\Core\CoolQ;
use App\Support\Log;

Log::setLoggerName(getenv('APP_NAME'));
Log::setLoggerPath(LOG_PATH);


$useWs = !empty(getenv('COOLQ_USE_WS')) ? getenv('COOLQ_USE_WS') : getenv('COOLQ_USE_HTTP');
$host = $useWs ? getenv('COOLQ_WS_HOST') . ':' . getenv('COOLQ_WS_PORT') : getenv('COOLQ_HTTP_HOST') . ':' . getenv('COOLQ_HTTP_PORT');

$app = new  CoolQ($host, getenv('COOLQ_TOKEN'), getenv('COOLQ_SECRET'), $useWs);


$app->attach(new \App\Plugin\TulingPlugin());
$app->attach(new \App\Plugin\MusicPlugin());
//$app->setReturnFormat('array');

return $app;
