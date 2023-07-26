<?php
namespace Illuminate;

use Illuminate\Http\Request;

class App {
    public static function run() {
        session_start();
        if (Request::isXhr()) {
            require($_SERVER['DOCUMENT_ROOT'] . '/../routes/api.php');
        } else {
            require($_SERVER['DOCUMENT_ROOT'] . '/../routes/web.php');
        }
    }
}