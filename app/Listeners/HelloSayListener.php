<?php

namespace App\Listeners;

use Symfony\Contracts\EventDispatcher\Event;

class HelloSayListener
{
    public function __construct()
    {
        
    }

    public function handle(Event $event)
    {
        echo microtime(true) . ' - hello.say event been handled!';
    }
}