<?php

return [


    'name' => env('APP_NAME', 'DianQ'),

    'token' => env('COOLQ_TOKEN', ''),

    'secret' => env('COOLQ_SECRET', ''),

    'driver' => env('COOLQ_DRIVER', 'http'),

    'coolq' => [

        'http' => [
            'host' => env('COOLQ_HTTP_HOST', '127.0.0.1'),
            'port' => env('COOLQ_HTTP_PORT', '5700'),
        ],

        'ws' => [
            'host' => env('COOLQ_HTTP_HOST', '127.0.0.1'),
            'port' => env('COOLQ_WS_PORT', '6700'),
        ],
    ],

    'is_white_list' => env('WHITE_LIST', true),

    'is_black_list' => env('BLACK_LIST', false),

    'member_list' => [

        'white' => [
            'private' => explode(',', env('PRIVATE_WHITE_LIST', ''))[0] == '' ? [] : explode(',', env('PRIVATE_WHITE_LIST', '')),
            'group' => explode(',', env('GROUP_WHITE_LIST', ''))[0] == '' ? [] : explode(',', env('PRIVATE_WHITE_LIST', '')),
            'discuss' => explode(',', env('DISCUSS_WHITE_LIST', ''))[0] == '' ? [] : explode(',', env('PRIVATE_WHITE_LIST', '')),
        ],

        'black' => [
            'private' => explode(',', env('PRIVATE_BLACK_LIST', ''))[0] == '' ? [] : explode(',', env('PRIVATE_BLACK_LIST', '')),
            'group' => explode(',', env('GROUP_BLACK_LIST', ''))[0] == '' ? [] : explode(',', env('GROUP_BLACK_LIST', '')),
            'discuss' => explode(',', env('DISCUSS_BLACK_LIST', ''))[0] == '' ? [] : explode(',', env('DISCUSS_BLACK_LIST', '')),
        ],
    ],


];
