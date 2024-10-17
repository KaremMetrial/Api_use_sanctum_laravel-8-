<?php

    use App\Http\Controllers\Api\AuthController;
    use App\Http\Controllers\Api\PostController;
    use App\Http\Controllers\Api\StatsController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
    */
//
//    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//        return $request->user();
//    });
//
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify', [AuthController::class, 'verifyCode']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('isVerified');

    Route::group(['middleware' => 'auth:sanctum'], function () {

        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('posts', PostController::class);
        Route::get('deleted-posts', [PostController::class, 'viewDeleted']);
        Route::post('posts/{id}/restore', [PostController::class, 'restore']);

    });


    Route::get('stats', [StatsController::class, 'stats']);

