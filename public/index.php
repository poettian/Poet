<?php

define('POET_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap.php';

$response = $app->make('http')->handle();

$response->send();
