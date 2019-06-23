<?php

namespace Poet\Http;

class Http
{   
    protected $app;
    protected $request;
    protected $response;

    public function __construct($app)
    {
        $this->app = $app;
        $this->request = app('request');
        $this->response = app('response');
    }

    public function handle()
    {
        list($c, $a, $p) = $this->request->init();
        $controller_class = "\\App\\Http\\Controllers\\{$c}";
        $controller = new $controller_class();
        $output = call_user_func([$controller, $a], $this->request, ...$p);
        
        return $this->response->parse($output);
    }
}