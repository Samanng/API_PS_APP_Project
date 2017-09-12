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
    Route::get('userProfile/{id}','RegisterUserController@userProfile');// get old value to update
    Route::get('viewUserFavorite/{id}','RegisterUserController@viewUserFavorite');



//    Route::put('updateUserInfo/{id}','RegisterUserController@updateUserInfo');
    Route::post('sendMail','RegisterUserController@sendMail');
    Route::post('resetForgotPass','RegisterUserController@resetForgotPass');

//     Route::put('updateUserInfo/{id}','RegisterUserController@updateUserInfo');

    Route::post('sendMail','RegisterUserController@sendMail');
    Route::post('resetForgotPass','RegisterUserController@resetForgotPass');

    Route::post('cover/{id}','RegisterUserController@changeCover');
    Route::post('profile/{id}','RegisterUserController@profile');
    Route::post('confirmUserEmail/{id}','RegisterUserController@confirmUserEmail');
    Route::post('changepassword/{id}','RegisterUserController@changePassword'); // change password
    Route::post('updateUserInfo/{id}','RegisterUserController@updateUserInfo');// update

    Route::delete('deleteFavorite/{id}','FavoritesController@deleteFavorite');


});


Route::group(array('prefix'=>'posters'), function(){
    Route::get('viewall','PostersController@index');
    Route::get('posterProfile/{id}','PostersController@posterProfile');// image profile
    Route::get('viewPosterPost/{id}','PostersController@viewPosterPost');

    Route::post('resetForgotPass','PostersController@resetForgotPass');
    Route::post('sendMail','PostersController@sendMail');
    Route::post('confirmPosterEmail/{id}','PostersController@confirmPosterEmail');
    Route::post('changepassword/{id}','PostersController@changePassword'); // change password

    Route::post('register','PostersController@register');
    Route::post('login','PostersController@login');
    Route::post('updatePosterInfo/{id}','PostersController@updatePosterInfo');
    Route::post('cover/{id}','PostersController@changeCover');
    Route::post('profile/{id}','PostersController@profile');

    Route::post('updateSellerInfo/{id}','PostersController@updateUserInfo');
    Route::get('updateSellerInfoData/{id}','PostersController@sellerOldDataUpdate');


});

Route::group(array('prefix'=>'posts'), function(){

    Route::get('search/{param}','PostsController@search');
	Route::get('viewEachCategories/{id}','PostsController@view_each_category');
    Route::get('viewAllPost/{page}','PostsController@index');
    Route::get('postDetail/{id}','PostsController@postDetail');

    Route::get("categories","CategoriesController@categoriesList");
    Route::get("listCategory/{catId}","CategoriesController@productEachCat");
    Route::post("comment","CommentsController@commentPost");
    Route::get('viewcmt/{id}','CommentsController@viewComment');
    Route::get('listcomment/{id}','CommentsController@listComment');
    Route::get("checkLike/{userId}/{postId}","LikesController@checkLike");
    Route::get('postOldDataUpdate/{id}','PostsController@postOldDataUpdate');
    Route::post('updateInfoPost/{id}','PostsController@updateInfoPost');

    Route::post('createPost','PostsController@create_post');
    Route::delete('deletePost/{id}','PostsController@deletePost');

    Route::post('updateImagePost','PostsController@uploadImage');

    Route::get('viewAllFav/{userId}','FavoritesController@index');
    Route::post('store','FavoritesController@store');

    });




