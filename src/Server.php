<?php
namespace Illuminate;

class Server {
    public static function getProtocol() {
        if (isset($_SERVER['HTTPS'])
            && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ) {
                return 'https';
        }
    
        return 'http';
    }
    
    public static function hostname() {
        return Server::get('HTTP_HOST', '127.0.0.1');
    }
    
    public static function siteUrl() {
        return Server::getProtocol() . '://' . Server::hostname();
    }

    public static function get($key, $default = '') {
        if (! isset($_SERVER[$key])) return $default;
        return $_SERVER[$key];
    }
}