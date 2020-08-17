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
        try {
            $this->includeFile();
            $callback = $this->router->dispatch();
            $output = call_user_func($callback);
            $this->response->parse($output);
        } catch (\Exception $e) {
            // @todo 这里要做处理
            echo '发生错误啦';
        }
    
        return $this->response;
    }
    
    /**
     * 包含路由文件
     */
    protected function includeFile()
    {
        $routePath = $this->app->routePath();
        if ($this->app->runningInConsole()) {
            require $routePath . '/cli.php';
        } else {
            require $routePath . '/web.php';
        }
    }
}