<?php


use App\Core\CoolQ;
use App\Support\Log;

Log::setLoggerName(getenv('APP_NAME'));
Log::setLoggerPath(LOG_PATH);


$useWs = !empty(getenv('COOLQ_USE_WS')) ? getenv('COOLQ_USE_WS') : getenv('COOLQ_USE_HTTP');

$useWs = $useWs == 'true' ? true : false;


if ($useWs && preg_match("/cli-server/i", php_sapi_name())) {
    die('must be used in PHP CLI mode');
}

$host = $useWs ? getenv('COOLQ_WS_HOST') . ':' . getenv('COOLQ_WS_PORT') : getenv('COOLQ_HTTP_HOST') . ':' . getenv('COOLQ_HTTP_PORT');


$app = new  CoolQ($host, getenv('COOLQ_TOKEN'), getenv('COOLQ_SECRET'), $useWs);

//$app->setIsAsync(true);
//$app->setReturnFormat('array');

$app->setIsWhiteList(getenv('WHITE_LIST') == 'true');
$app->setIsWhiteList(getenv('BLACK_LIST') == 'true');

$privateWhiteList = explode(',', getenv('PRIVATE_WHITE_LIST'));
$privateBlackList = explode(',', getenv('PRIVATE_BLACK_LIST'));
$groupWhiteList = explode(',', getenv('GROUP_WHITE_LIST'));
$groupBlackList = explode(',', getenv('GROUP_BLACK_LIST'));
$discussWhiteList = explode(',', getenv('DISCUSS_WHITE_LIST'));
$discussBlackList = explode(',', getenv('DISCUSS_BLACK_LIST'));


$app->setPrivateWhiteList($privateWhiteList);
$app->setPrivateBlackList($privateBlackList);
$app->setGroupWhiteList($groupWhiteList);
$app->setGroupWhiteList($groupBlackList);
$app->setDiscussWhiteList($discussWhiteList);
$app->setDiscussWhiteList($discussBlackList);

$app->attach(new \App\Plugin\GongGongPlugin());
$app->attach(new \App\Plugin\MusicPlugin());
$app->attach(new \App\Plugin\TulingPlugin());


return $app;
