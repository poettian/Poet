<?php

namespace App\Providers;

use Poet\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'hello.say' => [
            \App\Listeners\HelloSayListener::class,
        ],
    ];

    protected $subscribe = [];

    public function boot()
    {
        $events = $this->app['events'];
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->addListener($event, [new $listener, 'handle']);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            $events->addSubscriber($subscriber);
        }
    }
}