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
    Route::post('login','PostersController@login');
    Route::post('register','PostersController@register');
});

Route::group(array('prefix'=>'posts'), function(){
    Route::get('search/{param}','PostsController@search');// not complete yet
    Route::get("categories","PostsController@categoriesList");
    Route::get("listCategory/{catId}","PostsController@productEachCat");
    Route::post("comment","PostsController@commentPost");
    Route::get("checkLike/{userId}/{postId}","PostsController@checkLike");
    Route::get('show/{id}','PostsController@show');
});
