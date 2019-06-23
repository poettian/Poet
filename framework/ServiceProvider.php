<?php

namespace Poet;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

abstract class ServiceProvider implements ServiceProviderInterface
{

    protected $app;

    protected $defer = false;

    public function __construct()
    {
        $this->app = app();
    }

    public function register(Container $container)
    {
        //
    }
    
    
    public function provides()
    {
        return [];
    }

    public function when()
    {
        return [];
    }

    public function isDeferred()
    {

    }
}
