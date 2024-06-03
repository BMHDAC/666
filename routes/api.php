<?php


use Illuminate\Support\Facades\Route;

//
Route::get("/statistics", [\App\Http\Controllers\StatisticController::class, "index"]);
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'index']);
Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'index']);
Route::group(['namespace' => 'StressData', 'prefix' => 'stressdata'], function () {
    Route::get('/', [\App\Http\Controllers\StressData\StressDataController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\StressData\StressDataController::class, 'post_stress_data']);
    Route::get('/user/{id}', [\App\Http\Controllers\StressData\StressDataController::class, 'get_stress_data_by_user_id']);
    Route::get('/time/{id}', [\App\Http\Controllers\StressData\StressDataController::class, 'get_stress_data_by_user_id_at_date']);
    Route::get('time/analyzed/{id}', [\App\Http\Controllers\StressData\StressDataController::class, 'get_analyzed_data_by_user_id']);
    Route::delete('/{id}', [\App\Http\Controllers\StressData\StressDataController::class, 'delete_entry_by_id']);
});
Route::group(['namespace' => 'Firebase', 'prefix' => 'firebase'], function () {
    Route::post('/set', [\App\Http\Controllers\Firebase\FirebaseController::class, 'set_token']);
    Route::put('/set', [\App\Http\Controllers\Firebase\FirebaseController::class, 'update_token']);
});
Route::middleware('auth:api')->group(function () {
    Route::get('/me', "Auth\AuthController@me");
    Route::group(['namespace' => 'Post', 'prefix' => 'post'], function () {
        Route::get('gets', "PostController@getPosts");
        Route::post('add', "PostController@AddPost");
        Route::put('edit/{id}', "PostController@EditPost");
    });
    Route::group(['namespace' => 'Class', 'prefix' => 'class'], function () {
        Route::get('gets', "ClassController@getClass");
        Route::post('add', "ClassController@AddClass");
    });
});
