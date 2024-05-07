<?php

namespace Modules\TwoUsers\Providers;

use Two\Events\Dispatcher;
use Two\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the module.
     *
     * @var array
     */
    protected $listen = array(
        'Modules\TwoUsers\Events\SomeEvent' => array(
            'Modules\TwoUsers\Listeners\EventListener',
        ),
    );


    /**
     * Register any other events for your module.
     *
     * @param  \Two\Events\Dispatcher  $events
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        parent::boot($events);

        //
        $path = realpath(__DIR__ .'/../');

        // Load the Events.
        $path = $path .DS .'Events.php';

        $this->loadEventsFrom($path);
    }
}
