<?php
namespace Illuminate\Global;

use Illuminate\Global\Interfaces\IServer;

class Server implements IServer {
	public static function getProtocol() {
        if (isset($_SERVER['HTTPS'])
            && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ) {
                return 'https';
        }

        return 'http';
    }

    public static function hostName() {
        if (! isset($_SERVER['HTTP_HOST'])) return '127.0.0.1';
        return $_SERVER['HTTP_HOST'];
    }

    public static function siteUrl() {
        return Server::getProtocol() . '://' . Server::hostName();
    }
}