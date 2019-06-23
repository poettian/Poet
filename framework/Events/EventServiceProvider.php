<?php

namespace Poet\Events;

use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Poet\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register(Container $container)
    {
        $container['events'] = function ($c) {
            return new EventDispatcher();
        };
    }
}
