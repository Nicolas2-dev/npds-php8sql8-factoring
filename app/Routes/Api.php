<?php
/**
 * Two - Api
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| C'est ici que vous pouvez enregistrer les routes d'API pour votre application. Ces
| les routes sont chargées par le RouteServiceProvider au sein d'un groupe qui
| se voit attribuer le groupe middleware "api". Amusez-vous à créer votre API !
|
*/

use Two\Http\Request;
use Two\Support\Facades\Route;


Route::get('user', array('middleware' => 'auth:api', function (Request $request)
{
    return $request->user();
}));
