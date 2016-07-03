<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/home', 'HomeController@index');

    Route::get('/form/{id}/ratings/config', 'FormController@showRatingsConfig');
    Route::patch('/form/{id}/ratings/config', 'FormController@updateRatingsConfig');

    Route::resource('/form', 'FormController');

    Route::resource('/response', 'ResponseController', ['only' => [
        'show', 'update'
    ]]);

    Route::get('/profile', 'ProfileController@showProfile');

    Route::get('/auth/google/redirect',   ['as' => 'social.redirect',   'uses' => 'Auth\AuthController@redirectToProvider']);
    Route::get('/auth/google/handle',     ['as' => 'social.handle',     'uses' => 'Auth\AuthController@handleProviderCallback']);
});
