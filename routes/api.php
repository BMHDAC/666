<?php


use Illuminate\Support\Facades\Route;

//
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'index']);
Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', "Auth\AuthController@me");
    Route::group(['namespace' => 'Post', 'prefix' => 'post'], function () {
        Route::get('gets', "PostController@getPosts");
        Route::post('add', "PostController@AddPost");
    });

});


