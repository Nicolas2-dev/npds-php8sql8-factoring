<?php

use Npds\Http\Request;


//
// The global parameter patterns.

Route::pattern('slug', '(.*)');


//
// The application routes.

Route::get('/', array('uses' => 'Home@index', 'as' => 'home'));

Route::group(array('prefix' => 'samples'), function ()
{
    Route::get('database', 'Sample@database');
    Route::get('error',    'Sample@error');
    Route::get('redirect', 'Sample@redirect');
    Route::get('request',  'Sample@request');
});

Route::get('pages/{page?}', 'Sample@page')->where('page', '(.*)');

Route::get('blog/{slug}',   'Sample@post');

// A route executing a closure.
Route::get('test', function ()
{
    echo 'This is a test.';
});

// The default Auth Routes.
Route::get( 'login', array('middleware' => 'guest', 'uses' => 'Users@login'));
Route::post('login', array('middleware' => 'guest', 'uses' => 'Users@postLogin'));

Route::post( 'logout', array('middleware' => 'auth|csrf', 'uses' => 'Users@logout'));

// The User's Dashboard.
Route::get('dashboard', array('middleware' => 'auth', 'uses' => 'Users@dashboard'));

// The User's Profile.
Route::get( 'profile', array('middleware' => 'auth',      'uses' => 'Users@profile'));
Route::post('profile', array('middleware' => 'auth|csrf', 'uses' => 'Users@postProfile'));


// A route executing a closure and having own parameter patterns.
Route::get('language/{code}', array('uses' => function (Request $request, $code)
{
    $url = Config::get('app.url');

    $languages = Config::get('user_languages');

    if (array_key_exists($code, $languages) && Str::startsWith($request->header('referer'), $url)) {
        Session::set('user_language', $code);

        // Stockez également la langue actuelle dans un cookie d'une durée de cinq ans.
        Cookie::queue(PREFIX .'user_language', $code, Cookie::FIVEYEARS);
    }

    return Redirect::back();

}, 'where' => array('code' => '([a-z]{2})')));


// Show the PHP information.
Route::get('phpinfo', function ()
{
    ob_start();

    phpinfo();

    return Response::make(ob_get_clean(), 200);
});
