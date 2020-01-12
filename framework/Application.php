<?php

namespace Poet;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Poet\Events\EventServiceProvider;
use Poet\Config\ConfigServiceProvider;
use Poet\Http\HttpServiceProvider;

class Application extends Container
{
    protected static $instance;

    protected $basePath;

    protected $serviceProviders = [];

    protected $loadedProviders = [];

    protected $booted = false;

    public function __construct($basePath)
    {
        parent::__construct();

        static::setInstance($this);

        $this->setBasePath($basePath);

        $this->registerBaseServiceProviders();
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    protected static function setInstance(Container $container)
    {
        return static::$instance = $container;
    }

    protected function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    public function configPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config';
    }

    protected function registerBaseServiceProviders()
    {
        $this->register(new ConfigServiceProvider);
        $this->register(new EventServiceProvider);
        $this->register(new HttpServiceProvider);
    }

    public function register(ServiceProviderInterface $provider, array $values = array())
    {
        parent::register($provider, $values);

        $this->markAsRegistered($provider);

        if ($this->booted) {
            $this->bootProvider($provider);
        }
    }

    public function make($abstract)
    {
        if (! isset($this[$abstract])) {
            throw new Exception("Service {$abstract} has not been registed");
        }

        return $this[$abstract];
    }

    public function bootstrap()
    {
        $this->bootstrapProviders();
    }

    protected function bootstrapProviders()
    {
        $providers = $this['config']->get('app.providers');
        foreach ($providers as $provider) {
            $this->register(new $provider);
        }
        $this->boot();
    }

    protected function markAsRegistered($provider)
    {
        $this->serviceProviders[] = $provider;

        $this->loadedProviders[get_class($provider)] = true;
    }

    protected function boot()
    {
        if ($this->booted) {
            return;
        }

        array_walk($this->serviceProviders, function ($p) {
            $this->bootProvider($p);
        });

        $this->booted = true;
    }

    protected function bootProvider(ServiceProvider $provider)
    {
        if (method_exists($provider, 'boot')) {
            call_user_func([$provider, 'boot']);
        }
    }
}