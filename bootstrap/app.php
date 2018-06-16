<?php


use App\Core\CoolQ;

$app = new  CoolQ(getenv('COOLQ_HOST') . ':' . getenv('COOLQ_PORT'), getenv('COOLQ_TOKEN'), getenv('COOLQ_SECRET'));

//$app->setReturnFormat('array');


return $app;
