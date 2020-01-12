<?php

namespace Poet\Http;

class Response
{   

    protected $body;

    public function __construct($app)
    {
        
    }

    public function parse($output)
    {
        if (is_string($output)) {
            $this->body = $output;
        } elseif (is_array($output)) {
            $this->body = json_encode($output);
        }
        
        return $this;
    }

    public function send()
    {
        echo $this->body;
    }
}
