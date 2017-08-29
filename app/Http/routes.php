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
    Route::put('updateUserInfo/{id}','RegisterUserController@updateUserInfo');
    Route::get('sendMail','RegisterUserController@sendMail');
    Route::get('resetForgotPass','RegisterUserController@resetForgotPass');
    Route::post('cover/{id}','RegisterUserController@changeCover');
    Route::post('profile/{id}','RegisterUserController@profile');

    Route::post('changepassword/{id}','RegisterUserController@changePassword');

    Route::post('updateUserInfo/{id}','RegisterUserController@updateUserInfo');
});


Route::group(array('prefix'=>'posters'), function(){
    Route::get('viewall','PostersController@index');
    Route::get('posterProfile/{id}','PostersController@posterProfile');
    Route::get('viewPosterPost/{id}','PostersController@viewPosterPost');
    Route::post('changepassword/{id}','PostersController@changePassword');
    Route::post('register','PostersController@register');
    Route::post('login','PostersController@login');
    Route::post('register','PostersController@register');
    Route::put('updatePosterInfo/{id}','PostersController@updatePosterInfo');
    Route::post('cover/{id}','PostersController@changeCover');
    Route::post('profile/{id}','PostersController@profile');

    Route::post('updateSellerInfo/{id}','PostersController@updateUserInfo');

});

Route::group(array('prefix'=>'posts'), function(){

    Route::get('search/{param}','PostsController@search');
  
    Route::get('viewAllPost','PostsController@index');
    Route::get('postDetail/{id}','PostsController@postDetail');

    Route::get("categories","CategoriesController@categoriesList");
    Route::get("listCategory/{catId}","CategoriesController@productEachCat");
    Route::post("comment","CommentsController@commentPost");
    Route::get('viewcmt/{id}','CommentsController@viewComment');
    Route::get('listcomment/{id}','CommentsController@listComment');
    Route::get("checkLike/{userId}/{postId}","LikesController@checkLike");

    Route::post('updateInfoPost/{id}','PostsController@updateInfoPost');

    Route::post('createPost','PostsController@create_post');
    Route::delete('deletePost/{id}','PostsController@deletePost');

    Route::post('updateImagePost','PostsController@uploadImage');

    Route::get('viewAllFav/{userId}','FavoritesController@index');
    Route::post('store','FavoritesController@store');

    });


