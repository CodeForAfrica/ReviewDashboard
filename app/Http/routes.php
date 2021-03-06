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
        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect('/home');
        }
        return view('welcome');
    });

    Route::get('/home', 'HomeController@index');

    Route::get('/form/{id}/ratings/config', 'FormController@showReviewConfig');
    Route::patch('/form/{id}/ratings/config', 'FormController@updateReviewConfig');

    Route::get('/form/{id}/share', 'FormController@showUsers');
    Route::post('/form/{id}/share', 'FormController@updateUsers');
    Route::delete('/form/{id}/share', 'FormController@deleteUsers');

    Route::resource('/form', 'FormController');

    Route::resource('/response', 'ResponseController', ['only' => [
        'show', 'update'
    ]]);

    Route::get('/profile', 'ProfileController@showProfile');
    Route::post('/profile', 'ProfileController@updateProfile');

    Route::get('/auth/google/redirect',   ['as' => 'social.redirect',   'uses' => 'Auth\AuthController@redirectToProvider']);
    Route::get('/auth/google/handle',     ['as' => 'social.handle',     'uses' => 'Auth\AuthController@handleProviderCallback']);
});
