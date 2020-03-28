<?php

use Poet\Application;

if (! function_exists('app')) {
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Application::getInstance();
        }

        return Application::getInstance()->make($abstract, $parameters);
    }
}

if (! function_exists('event')) {
    function event($event)
    {
        return app('events')->dispatch($event, $event::NAME);
    }
}

if (! function_exists('config')) {
    function config($key, $default = null)
    {
        return app('config')->get($key, $default);
    }
}
