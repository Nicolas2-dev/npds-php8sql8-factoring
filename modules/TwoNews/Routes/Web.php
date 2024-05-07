<?php

/*
|--------------------------------------------------------------------------
| Module Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for the module.
| It's a breeze. Simply tell Two the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'StartPage@start');


Route::group(array('prefix' => 'two_news'), function ()
{
    Route::get('/', function ()
    {
        dd('This is the TwoNews Module index page.');
    });
});
