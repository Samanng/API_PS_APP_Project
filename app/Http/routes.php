<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(array('prefix'=>'users'), function(){
	Route::get('view','RegisterUserController@index');
	Route::post('create','RegisterUserController@store');
});

Route::group(array('prefix'=>'posters'), function(){
    Route::get('view','PostersController@index');
    Route::post('create','PostersController@store');
});