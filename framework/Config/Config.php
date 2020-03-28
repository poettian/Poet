<?php

namespace Poet\Config;

use Pimple\Container;

class Config
{
    protected $path;
    
    protected $items;

    public function __construct(Container $app)
    {
        $this->path = $app->configPath();

        $this->loadConfigurationFiles();
    }

    protected function loadConfigurationFiles()
    {
        foreach (glob("{$this->path}/*.php") as $filename) {
            $key = basename($filename, '.php');
            $this->items[$key] = include_once $filename;
        }
    }

    public function get($key)
    {
        $value = $this->items;
        foreach (explode('.', $key) as $index) {
            $value = $value[$index];
        }   
        return $value;
    }
}