<?php
/**
 * Two - Web
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| C'est ici que vous pouvez enregistrer des routes Web pour votre application. Ces
| les routes sont chargées par le RouteServiceProvider au sein d'un groupe qui
| contient le groupe middleware "web". Créez maintenant quelque chose de génial !
|
*/

use Two\Support\Str;
use Two\Http\Request;
use Two\Support\Facades\Route;
use Two\Support\Facades\Config;
use Two\Support\Facades\Cookie;
use Two\Support\Facades\Session;
use Two\Support\Facades\Redirect;
use Two\Support\Facades\Response;

/**
 * Les pages statiques.
 */
// Route::get('/', 'Pages@show');

// Route::get('pages/{slug}', 'Pages@show')->where('slug', '(.*)');

/**
 * Le changeur de langue.
 */
Route::get('language/{language}', function (Request $request, $language)
{
    $url = Config::get('app.url');

    $languages = Config::get('languages');

    if (array_key_exists($language, $languages) && Str::startsWith($request->header('referer'), $url)) {
        Session::set('language', $language);

        // Stockez également la langue actuelle dans un cookie d'une durée de cinq ans.
        Cookie::queue(PREFIX .'language', $language, Cookie::FIVEYEARS);
    }

    return Redirect::back();

})->where('language', '([a-z]{2})');


/**
 * Afficher les informations PHP.
 */
Route::get('phpinfo', function ()
{
    ob_start();

    phpinfo();

    return Response::make(ob_get_clean(), 200);
});
