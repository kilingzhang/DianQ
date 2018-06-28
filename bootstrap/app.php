<?php


use App\Core\CoolQ;
use App\Support\Log;

Log::setLoggerName(getenv('APP_NAME'));
Log::setLoggerPath(LOG_PATH);


$app = new  CoolQ(getenv('COOLQ_HOST') . ':' . getenv('COOLQ_PORT'), getenv('COOLQ_TOKEN'), getenv('COOLQ_SECRET'));

$app->attach(new \App\Plugin\TulingPlugin());
$app->attach(new \App\Plugin\MusicPlugin());

//$app->setReturnFormat('array');

return $app;
