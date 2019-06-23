<?php

namespace Poet\Http;

class Request
{
    public function __construct($app)
    {
        
    }   

    public function init()
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);
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

        return [$controller, $action, $parameters];
    }
}