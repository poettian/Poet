<?php

namespace App\Http\Controllers;

use App\Events\HelloSayEvent;
use Redis;
use Exception;

class Home
{
    public function index()
    {
        echo 'lab.dev.com';
    }

    public function say()
    {   
        // dispatch events
        event(new HelloSayEvent());
    }

    public function redis()
    {
        try {
            $redis = new Redis();
            if (! $redis->connect('127.0.0.1', 6379, 5, null, 200)) {
                throw new Exception('can not connect to redis');
            }
            if (! $redis->auth('secret')) {
                throw new Exception('authenticate failed');
            }
            if (! $redis->select(0)) {
                throw new Exception('select db failed');
            }
            $redis->setOption(Redis::OPT_PREFIX, 'lab.dev.com:');	// use custom prefix on all keys

            $value = $redis->get('start');
            var_dump($value);

            $redis->close();
        } catch (\RedisException $e) {
            die($e->getMessage());
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}
