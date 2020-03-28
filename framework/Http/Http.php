<?php

namespace Poet\Http;

class Http
{   
    protected $app;
    protected $router;
    protected $request;
    protected $response;

    public function __construct($app)
    {
        $this->app      = $app;
        $this->router   = app('router');
        $this->request  = app('request');
        $this->response = app('response');
    }

    public function handle()
    {
        do {
            list($controller, $action, $parameters) = $this->router->init();
            $controller_class = "\\App\\Http\\Controllers\\{$controller}";
            if (!class_exists($controller_class)) {
                $this->response->setStatusCode('404')->parse('request path not exists');
                break;
            }
            $controller_obj = new $controller_class();
            if (!method_exists($controller_obj, $action)) {
                $this->response->setStatusCode('404')->parse('request path not exists');
                break;
            }
            $output = call_user_func([$controller_obj, $action], ...$parameters);
            $this->response->parse($output);
        } while (false);
        
        return $this->response;
    }
}