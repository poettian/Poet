<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap.php';

$response = $app->make('http')->handle();

$response->send();