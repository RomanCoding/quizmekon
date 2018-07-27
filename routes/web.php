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

Route::post('/youtube/search', 'YoutubeController@search');
Route::post('/youtube/videos', 'YoutubeController@videos');


Route::group(['middleware' => 'auth'], function() {
    Route::post('/quizzes', 'QuizController@store');
    Route::post('/quizzes/bulk', 'QuizController@storeBulk');

    Route::post('/polls/{quiz}', 'PollController@store');

    Route::patch('/quizzes/{quiz}/vote', 'VoteController@updateQuiz')->middleware('confirmed');
    Route::patch('/comments/{comment}/vote', 'VoteController@updateComment')->middleware('confirmed');

    Route::post('/comments/quiz/{quiz}', 'CommentController@storeQuiz')->middleware(['cd.comment', 'confirmed']);
    Route::post('/comments/comment/{comment}', 'CommentController@storeComment')->middleware(['cd.comment', 'confirmed']);

    Route::get('/comments/{post}', 'CommentController@index');
});




Route::get('/verifyemail/{token}', 'Auth\RegisterController@verify');

Route::post('/login/check', 'Auth\LoginController@checkIfCorrect');

Route::get('/categories', 'CategoryController@index');
Route::get('/quizzes', 'QuizController@index');
Route::get('/quizzes/{quiz}', 'QuizController@show');



Route::post('/register/email', 'Auth\RegisterController@checkUniqueEmail');
Route::post('/register/username', 'Auth\RegisterController@checkUniqueUsername');

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/sessionStatus', function () {
    return ['user' => Auth::user() ? Auth::user() : null];
});

Route::get('/{vue?}', 'HomeController@index')->where('vue', '[\/\w\.-]*');