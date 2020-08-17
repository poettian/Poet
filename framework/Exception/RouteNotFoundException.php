<?php


namespace Poet\Exception;


class RouteNotFoundException extends \Exception
{
    public function __construct()
    {
        $message = 'file not found';
        parent::__construct($message);
    }
}