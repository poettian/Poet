<?php

namespace Poet\Config;

use Pimple\Container;
use Poet\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(Container $container)
    {
        $container['config'] = function ($c) {
            return new Config($c);
        };
    }
}
