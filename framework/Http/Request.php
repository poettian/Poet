<?php

namespace Poet\Http;

class Request
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }   
    
}