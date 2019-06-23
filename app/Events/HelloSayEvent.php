<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class HelloSayEvent extends Event
{
    public const NAME = 'hello.say';

    public function __construct()
    {
        
    }
}