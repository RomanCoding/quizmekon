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

Route::get('/posts', 'PostController@index');
Route::get('/posts/{post}', 'PostController@show');

Route::get('/comments/{post}', 'CommentController@index');
Route::post('/comments/post/{post}', 'CommentController@storePost');
Route::post('/comments/comment/{comment}', 'CommentController@storeComment');
Route::patch('/posts/{post}/vote', 'VoteController@update');

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/sessionStatus', function() {
    return ['user' => Auth::user() ? Auth::user() : null];
});
