<?php
namespace Illuminate;

use Illuminate\Http\Request;

class App {
    public static function run() {
        App::parseEnv();
        session_start();
        if (Request::isXhr()) {
            require($_SERVER['DOCUMENT_ROOT'] . '/../routes/api.php');
        } else {
            require($_SERVER['DOCUMENT_ROOT'] . '/../routes/web.php');
        }
    }

    private static function parseEnv() {
        if (! file_exists($_SERVER['DOCUMENT_ROOT'] . '/../.env')) {
            die('.env file not found');
        }
        $env = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../.env');
        $env = explode("\n", $env);
        foreach ($env as $config) {
            $config = trim($config);
            if (strpos($config, '=') !== false) {
                $config = explode('=', $config);
                define($config[0], $config[1]);
            }
        }
    }
}