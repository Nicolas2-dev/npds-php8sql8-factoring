<?php

namespace Modules\TwoCore\Providers;

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
        // Update counter table
        'Modules\TwoCore\Events\CounterUpdateEvent' => array(
            'Modules\TwoCore\Listeners\CounterUpdateEventListener',
        ),
        // Update referer table
        'Modules\TwoCore\Events\RefererUpdateEvent' => array(
            'Modules\TwoCore\Listeners\RefererUpdateEventListener',
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
