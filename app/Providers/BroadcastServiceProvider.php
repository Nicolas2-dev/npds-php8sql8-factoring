<?php
/**
 * Two - BroadcastServiceProvider
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

namespace App\Providers;

use Two\Http\Request;
use Two\Routing\Router;
use Two\Support\Facades\Broadcast;
use Two\Support\ServiceProvider;


class BroadcastServiceProvider extends ServiceProvider
{

    /**
     * Amorcez tous les services d'application.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->post('broadcasting/auth', array('middleware' => 'web', function (Request $request)
        {
            return Broadcast::authenticate($request);
        }));

        require app_path('Routes/Channels.php');
    }

    /**
     * Enregistrez le fournisseur de services de l'application.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
