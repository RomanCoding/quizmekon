<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/verifyemail/{token}', 'Auth\RegisterController@verify');

Route::post('/login/check', 'Auth\LoginController@checkIfCorrect');

Route::get('/categories', 'CategoryController@index');
Route::get('/posts', 'PostController@index');
Route::post('/posts', 'PostController@store')->middleware(['cd.post', 'confirmed']);
Route::get('/posts/{post}', 'PostController@show');

Route::get('/comments/{post}', 'CommentController@index');

Route::post('/comments/post/{post}', 'CommentController@storePost')->middleware(['cd.comment', 'confirmed']);
Route::post('/comments/comment/{comment}', 'CommentController@storeComment')->middleware(['cd.comment', 'confirmed']);

Route::patch('/posts/{post}/vote', 'VoteController@updatePost')->middleware('confirmed');
Route::patch('/comments/{comment}/vote', 'VoteController@updateComment')->middleware('confirmed');

Route::post('/register/email', 'Auth\RegisterController@checkUniqueEmail');
Route::post('/register/username', 'Auth\RegisterController@checkUniqueUsername');

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/sessionStatus', function () {
    return ['user' => Auth::user() ? Auth::user() : null];
});

Route::get('/{vue?}', 'HomeController@index')->where('vue', '[\/\w\.-]*');