<?php

use Poet\Http\Request;

$router = app('router');

// 闭包
$router->get('/', function () {
    echo 'Hello World';
});

// 控制器
$router->get('/tt', 'Home@index');

// 路由组
//$router->group([], function () {
//
//});