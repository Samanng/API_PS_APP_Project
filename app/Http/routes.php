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
    Route::post('login','RegisterUserController@login');
    Route::post('register','RegisterUserController@register');
    Route::get('userProfile/{id}','RegisterUserController@userProfile');
    Route::get('viewUserFavorite/{id}','RegisterUserController@viewUserFavorite');
    Route::post('cover/{id}','RegisterUserController@changeCover');
    Route::post('profile/{id}','RegisterUserController@profile');
});

Route::group(array('prefix'=>'posters'), function(){
    Route::post('register','PostersController@register');
    Route::post('login','PostersController@login');
    Route::post('register','PostersController@register');
    Route::get('sellerProfile/{id}','PostersController@sellerProfile');
    Route::get('viewPosterPost/{id}','PostersController@viewPosterPost');// Their post in their profile
    Route::post('cover/{id}','PostersController@changeCover');
    Route::post('profile/{id}','PostersController@profile');
});

Route::group(array('prefix'=>'posts'), function(){

    Route::get('search/{param}','PostsController@search');// not complete yet

    Route::get("categories","CategoriesController@categoriesList");
    Route::get("listCategory/{catId}","CategoriesController@productEachCat");

    Route::post("comment","CommentsController@commentPost");
    Route::get('viewcmt/{id}','CommentsController@viewComment');
    Route::get('listcomment/{id}','CommentsController@listComment');

    Route::get("checkLike/{userId}/{postId}","LikesController@checkLike");

    Route::get('postDetail/{id}','PostsController@postDetail');
    Route::delete('deletePost/{id}','PostsController@deletePost');
    Route::post('createPost','PostsController@create_post');


});
