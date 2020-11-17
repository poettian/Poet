<?php

use Poet\Http\Request;

$router = app('router');

// 闭包
$router->get('/', function () {
    echo 'Hello World';
});

// 控制器
$router->post('test', 'Home@test');

// 路由组
//$router->group([], function () {
//
//});