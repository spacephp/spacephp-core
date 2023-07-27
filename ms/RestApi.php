<?php
namespace MS;

use Illuminate\Route;
use MS\Controllers\ApiController;

class RestApi {
    public static function route() {
        Route::group(['namespace' => '/restapi/v1'], function () {
            Route::delete('/{table}/{id}', [ApiController::class, 'destroy']);
            Route::get('/{table}/{id}', [ApiController::class, 'show']);
            Route::post('/{table}', [ApiController::class, 'store']);
            Route::put('/{table}/{id}', [ApiController::class, 'update']);
        
            Route::get('/{table}', [ApiController::class, 'index']);
        });
    }
}