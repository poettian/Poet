<?php

namespace App\Http\Controllers;

use App\Events\HelloSayEvent;

class Home
{
    public function index()
    {
        return 'Poet Framework';
    }

    public function event()
    {   
        // dispatch events
        event(new HelloSayEvent());
    }
}
