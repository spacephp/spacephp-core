<?php

use Illuminate\Route;

use MS\Controllers\AuthController;
use MS\Controllers\Admin\AdminController;
use MS\Controllers\Admin\CollectionController;
use MS\Controllers\SiteController;
use MS\Controllers\BlogController;





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