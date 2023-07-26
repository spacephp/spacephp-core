<?php 
namespace Illuminate\Http;

class Session {
    public static function getOnce($key, $default = '') {
        $result = Session::get($key, $default);
        unset($_SESSION[$key]);
        return $result;
    }
    public static function get($key, $default = '') {
        if (! isset($_SESSION[$key])) return $default;
        return $_SESSION[$key];
    }
}