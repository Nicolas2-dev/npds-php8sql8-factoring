<?php
/**
 * Two - EventServiceProvider
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

namespace App\Providers;

use Two\Events\Dispatcher;
use Two\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * Les mappages d'écouteurs d'événements pour l'application.
     *
     * @var array
     */
    protected $listen = array(
        'App\Events\SomeEvent' => array(
            'App\Listeners\EventListener',
        ),
    );


    /**
     * Enregistrez tout autre événement pour votre application.
     *
     * @param  \Two\Events\Dispatcher  $events
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        parent::boot($events);

        //
        $path = app_path('Events.php');

        $this->loadEventsFrom($path);
    }
}
