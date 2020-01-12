<?php


namespace Poet\Redis;

use Pimple\Container;
use Poet\ServiceProvider;

class RedisServiceProvider extends ServiceProvider
{
    public function register(Container $container)
    {
        $container['redis'] = function ($c) {
            return new RedisManager($c);
        };
    }
}