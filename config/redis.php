<?php

return [
    'default' => [
        'driver' => 'single',
        'persistent' => true,
        'host' => '10.0.32.85',
        'port' => '6380',
        'timeout' => 1,
        'password' => '',
        'database' => 0,
        'options' => [
            \Redis::OPT_PREFIX => 'poet:',
            \Redis::OPT_SERIALIZER => \Redis::SERIALIZER_JSON,
        ],
    ],
];