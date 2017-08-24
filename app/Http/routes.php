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
    Route::post('register','RegisterUserController@register');
});

Route::group(array('prefix'=>'posters'), function(){
    Route::get('viewall','PostersController@index');
    Route::post('login','PostersController@login');
    Route::post('register','PostersController@register');
});

Route::group(array('prefix'=>'posts'), function(){
    Route::get('viewall','PostsController@index');
    Route::post('store','PostsController@store');
    Route::get('show/{id}','PostsController@show');
    Route::put('update/{id}','PostsController@update');
});

Route::group(array('prefix'=>'favorites'),function(){
    Route::get('viewall','FavoritesController@index');
    Route::post('store','FavoritesController@store');
});
