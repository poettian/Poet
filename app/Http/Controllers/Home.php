<?php

namespace App\Http\Controllers;

use App\Events\HelloSayEvent;
use Exception;

class Home
{
    public function index()
    {
	    phpinfo();exit;
        $datetime = date('Y-m-d H:i:s');
        return "北京时间 {$datetime}";
    }

    public function load()
    {
        $data = [
            'action' => 'addCourseClass',
            'data' => [
                'course_id' => 1000000,
                'class_id' => 10000000,
            ]
        ];

        
    }

    public function say()
    {   
        // dispatch events
        event(new HelloSayEvent());
    }

    public function redis()
    {
        $value = app('redis')->get('test');

        print_r($value);
    }
}
