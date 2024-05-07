<?php
/**
 * Two - AppServiceProvider
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

namespace App\Providers;

use Two\Foundation\Support\Providers\AppServiceProvider as ServiceProvider;
use Two\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Amorcez les événements d'application.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
      * Enregistrez le fournisseur de services de l'application.
      *
      * Ce fournisseur de services est un endroit pratique pour enregistrer vos modules
      * services dans le conteneur IoC. Si vous le souhaitez, vous pouvez faire des
      * méthodes ou fournisseurs de services pour garder le code plus ciblé et granulaire.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
