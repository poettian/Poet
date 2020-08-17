<?php

namespace Poet\Http;

use Poet\Exception\RouteNotFoundException;

class Router
{
    protected $app;
    
    protected $routes = [];
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function dispatch()
    {
        return $this->app->runningInConsole() ? $this->cliDispatch() : $this->webDispatch();
    }
    
    protected function cliDispatch()
    {
    
    }
    
    protected function webDispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $path = $uri['path'];
        if (!isset($this->routes[$method][$path])) {
            throw new RouteNotFoundException();
        }
        $handler = $this->routes[$method][$path];
        if ($handler instanceof \Closure) {
            $callback = $handler;
        } else {
            // @todo 这里要做空字符串的处理
            list($controllerName, $actionName) = explode('@', $handler);
            $controllerName = "\\App\\Http\\Controllers\\{$controllerName}";
            if (!class_exists($controllerName)) {
                throw new \Exception('controller not found');
            }
            $controller = new $controllerName();
            if (!method_exists($controller, $actionName)) {
                throw new \Exception('action not found');
            }
            $callback = [$controller, $actionName];
        }
        
        return $callback;
    }
    
    public function get(string $route, $handler)
    {
        $route = '/' . trim($route, '/');
        $this->routes['get'][$route] = $handler;
    }
}