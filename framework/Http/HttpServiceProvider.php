<?php

namespace Poet\Http;

use Pimple\Container;
use Poet\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    public function register(Container $container)
    {
        $container['http'] = function ($c) {
            return new Http($c);
        };
    
        $container['router'] = function ($c) {
            return new Router($c);
        };
        
        $container['request'] = function ($c) {
            return new Request($c);
        };

        $container['response'] = function($c) {
            return new Response($c);
        };
    }
}