<?php

namespace Poet\Http;

class Router
{
    protected $app;
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function init()
    {
        if ($this->app->runningInConsole()) {
            $uri = parse_url($_SERVER['argv'][1] ?? '/');
        } else {
            $uri = parse_url($_SERVER['REQUEST_URI']);
        }
        $path = $uri['path'];
        if ($path == '/') {
            return ['Home', 'index', []];
        }
        $controller = strtok($path, '/');
        $action = strtok('/');
        if ($action === false) {
            $action = 'index';
            return [$controller, $action, []];
        }
        $parameters = [];
        while ($v = strtok('/') !== false) {
            $parameters[] = $v;
        }
        
        return [ucfirst($controller), strtolower($action), $parameters];
    }
}