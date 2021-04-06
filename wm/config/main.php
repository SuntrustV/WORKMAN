<?php


global $config;

$config = [
    'db' => $db,
    'params' => $params,
    'domain_local' => 'websocket://0.0.0.0',
    'redis' => require(__DIR__ . DIRECTORY_SEPARATOR . 'redis' . DIRECTORY_SEPARATOR . 'redis.php'),
    'context' => [
        'ssl' => [
            'local_cert' => '/var/www/www-root/data/fullchain.pem',
            'local_pk' => '/var/www/www-root/data/privatkey.key',
            'verify_peer' => false,
        ]
    ]
];
