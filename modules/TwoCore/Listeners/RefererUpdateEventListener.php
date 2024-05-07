<?php

namespace Modules\TwoCore\Listeners;


use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\Security;
use Modules\TwoCore\Events\RefererUpdateEvent;


class RefererUpdateEventListener
{
    
    /**
     * 
     */
    private $config;


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = Config::get('two_core::config');
    }

    /**
     * Handle the event.
     *
     * @param  Modules\TwoCore\Evernts\RefererUpdateEvent  $event
     * @return void
     */
    public function handle(RefererUpdateEvent $event)
    {
        if ($this->config['httpref'] == 1) {
            
            $http_referer = $this->get_http_referer($event);

            $referer = htmlentities(strip_tags(Security::remove($http_referer ?? '')), ENT_QUOTES, 'utf-8');
            
            if ($referer != '' 
                and !strstr($referer, "unknown") 
                and !stristr($referer, $this->get_server_name($event))) 
            {
                DB::table('referer')->insert(['url' => $referer]);
            }
        }
    }

    /**
     * 
     */
    private function get_http_referer($event)
    {
        return $event->request->server('HTTP_REFERER');
    }

    /**
     * 
     */
    private function get_server_name($event)
    {
        return $event->request->server('SERVER_NAME');
    }

}
