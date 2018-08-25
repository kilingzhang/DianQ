<?php


use App\Core\CoolQ;
use App\Support\Log;
use CoolQSDK\Response;

Log::setLoggerName(getenv('APP_NAME'));
Log::setLoggerPath(LOG_PATH);


$useWs = !empty(getenv('COOLQ_USE_WS')) ? getenv('COOLQ_USE_WS') : getenv('COOLQ_USE_HTTP');

$useWs = $useWs == 'true' ? true : false;


$host = $useWs ? getenv('COOLQ_WS_HOST') . ':' . getenv('COOLQ_WS_PORT') : getenv('COOLQ_HTTP_HOST') . ':' . getenv('COOLQ_HTTP_PORT');


$app = new  CoolQ($host, getenv('COOLQ_TOKEN'), getenv('COOLQ_SECRET'), $useWs);


//$app->setIsAsync(true);
//$app->setReturnFormat('array');

$app->setIsWhiteList(getenv('WHITE_LIST') == 'true');
$app->setIsBlackList(getenv('BLACK_LIST') == 'true');


$privateWhiteList = explode(',', getenv('PRIVATE_WHITE_LIST'));
$privateBlackList = explode(',', getenv('PRIVATE_BLACK_LIST'));
$groupWhiteList = explode(',', getenv('GROUP_WHITE_LIST'));
$groupBlackList = explode(',', getenv('GROUP_BLACK_LIST'));
$discussWhiteList = explode(',', getenv('DISCUSS_WHITE_LIST'));
$discussBlackList = explode(',', getenv('DISCUSS_BLACK_LIST'));


$app->setPrivateWhiteList($privateWhiteList);
$app->setPrivateBlackList($privateBlackList);
$app->setGroupWhiteList($groupWhiteList);
$app->setGroupBlackList($groupBlackList);
$app->setDiscussWhiteList($discussWhiteList);
$app->setDiscussBlackList($discussBlackList);


$app->attach(new \App\Plugin\TulingPlugin());
$app->attach(new \App\Plugin\GongGongPlugin());
//$app->attach(new \App\Plugin\MusicPlugin());


return $app;
