<?php


use App\Core\DianQ;
use App\Support\Log;

Log::setLoggerName(getenv('APP_NAME'));
Log::setLoggerPath(LOG_PATH);


$useWs = !empty(getenv('COOLQ_USE_WS')) ? getenv('COOLQ_USE_WS') : getenv('COOLQ_USE_HTTP');

$useWs = $useWs == 'true' ? true : false;


$host = $useWs ? getenv('COOLQ_WS_HOST') . ':' . getenv('COOLQ_WS_PORT') : getenv('COOLQ_HTTP_HOST') . ':' . getenv('COOLQ_HTTP_PORT');
$token = getenv('COOLQ_TOKEN');
$secret = getenv('COOLQ_SECRET');

$isWhiteList = getenv('WHITE_LIST') == 'true';
$isBlackList = getenv('BLACK_LIST') == 'true';

$privateWhiteList = explode(',', getenv('PRIVATE_WHITE_LIST'));
$privateBlackList = explode(',', getenv('PRIVATE_BLACK_LIST'));
$groupWhiteList = explode(',', getenv('GROUP_WHITE_LIST'));
$groupBlackList = explode(',', getenv('GROUP_BLACK_LIST'));
$discussWhiteList = explode(',', getenv('DISCUSS_WHITE_LIST'));
$discussBlackList = explode(',', getenv('DISCUSS_BLACK_LIST'));


$app = new DianQ($host, $token, $secret);

$app->register(
    [
        'protocol' => Kilingzhang\QQ\Core\Protocols\GuzzleProtocol::class,
        'driver' => \Kilingzhang\QQ\CoolQ\QQ::class,
    ]
);


$app->QQ()->setIsWhiteList($isWhiteList);
$app->QQ()->setIsBlackList($isBlackList);
$app->QQ()->setPrivateWhiteList($privateWhiteList);
$app->QQ()->setPrivateBlackList($privateBlackList);
$app->QQ()->setGroupWhiteList($groupWhiteList);
$app->QQ()->setGroupBlackList($groupBlackList);
$app->QQ()->setDiscussWhiteList($discussWhiteList);
$app->QQ()->setDiscussBlackList($discussBlackList);


$app->attach(new \App\Plugins\TulingPlugin());
$app->attach(new \App\Plugins\GongGongPlugin());
//$app->attach(new \App\Plugins\MusicPlugin());


return $app;
