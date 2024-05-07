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


Route::group(array('prefix' => 'two_session_log'), function ()
{
    Route::get('/', function ()
    {
        dd('This is the TwoSessionLog Module index page.');
    });
});
