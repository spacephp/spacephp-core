<?php

use Illuminate\Route;

use MS\Controllers\AuthController;
use MS\Controllers\Admin\AdminController;
use MS\Controllers\Admin\CollectionController;
use MS\Controllers\SiteController;
use MS\Controllers\BlogController;
use MS\Controllers\ApiController;

define('ADMIN_VIEW', __DIR__ . '/views');
define('CACHE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/../cache/');

Route::group(['namespace' => '/restapi/v1'], function () {
    Route::delete('/{table}/{id}', [ApiController::class, 'destroy']);
    Route::get('/{table}/{id}', [ApiController::class, 'show']);
    Route::post('/{table}', [ApiController::class, 'store']);
    Route::put('/{table}/{id}', [ApiController::class, 'update']);

    Route::get('/{table}', [ApiController::class, 'index']);
});

Route::group(['namespace' => '/ms'], function () {
    Route::post('/v1/settings',                 [SiteController::class, 'saveSettings']);
    Route::post('/v1/contacts',                 [SiteController::class, 'contact']);
    Route::post('/v1/subscribers',              [SiteController::class, 'subscribe']);
    Route::post('/v1/auth/register',            [AuthController::class, 'register']);
    Route::post('/v1/auth/login',               [AuthController::class, 'login']);
});

Route::group(['namespace' => '/myadmin'], function () {
    Route::get('/',             [AdminController::class, 'index']);
    Route::get('/settings',     [AdminController::class, 'settings']);
    Route::get('/stats',        [AdminController::class, 'settings']);
    Route::get('/themes',       [AdminController::class, 'settings']);
    Route::get('/login',        [AdminController::class, 'login']);
    Route::get('/register',     [AdminController::class, 'register']);
    
    Route::get('/{collection}',           [CollectionController::class, 'index']);
    Route::get('/{collection}/create',    [CollectionController::class, 'create']);
    Route::post('/{collection}',          [CollectionController::class, 'store']);
    Route::get('/{collection}/{id}',      [CollectionController::class, 'show']);
    Route::get('/{collection}/{id}/edit', [CollectionController::class, 'edit']);
    Route::put('/{collection}/{id}',      [CollectionController::class, 'update']);
    Route::delete('/{collection}/{id}',   [CollectionController::class, 'destroy']);
});

Route::get('/blog',                     [BlogController::class, 'blog']);
Route::get('/2023/{month}/{slug}',      [BlogController::class, 'single']);
Route::get('/2024/{month}/{slug}',      [BlogController::class, 'single']);
Route::get('/label/{slug}',             [BlogController::class, 'label']);
Route::get('/c/{slug}',                 [BlogController::class, 'category']);
Route::get('/p/{slug}',                 [BlogController::class, 'page']);

class Site {
    public static $site = null;

    private static function getSite() {
        if (! Site::$site) {
            Site::$site = \MS\Models\Site::getSite();
        }
        return Site::$site;
    }

    public static function key($key, $default = '') {
        $site = Site::getSite();
        if (! isset($site->{$key})) {
            return $default;
        }
        return $site->{$key};
    }
}

class Data {
    public static function posts() {
        return \MS\Models\Post::getPosts();
    }
}