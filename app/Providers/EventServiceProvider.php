<?php

declare(strict_types=1);

namespace App\Providers;

use Npds\Events\Dispatcher;
use Npds\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = array(
        'App\Events\SomeEvent' => array(
            'App\Listeners\EventListener',
        ),
    );


    /**
     * Register any other events for your application.
     *
     * @param  \Npds\Events\Dispatcher  $events
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        parent::boot($events);

        //
        require app_path('Events.php');
    }
}