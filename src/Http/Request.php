<?php
namespace Illuminate\Http;

class Request {
    function __construct() {
        $this->uri = strtok($_SERVER['REQUEST_URI'], '?');
        $this->uri = explode('/', $this->uri);
    }

    public static function json($required = [])
    {
        $content = trim(file_get_contents("php://input"));
        if (! Request::isJson($content)) {
            return false;
        }
        $data = json_decode($content, true);
        foreach ($required as $item) {
            if (! isset($data[$item])) {
                return false;
            }
        }
        return $data;
    }

    public static function validate($fields) {
        $data = Request::json();
        foreach ($fields as $field => $validate) {
            $validate = explode('|', $validate);
            foreach ($validate as $item) {
                if (strpos($item, ':') !== false) {
                    $item = explode(':', $item);
                    $value = $item[1];
                    $item = $item[0];
                }
                switch ($item) {
                    case 'required':
                        if (! isset($data[$field]) || ! $data[$field]) {
                            Response::json(['message' => $field . ' is required'], 400);
                        }
                        break;
                    case 'email':
                        if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            Response::json(['message' => 'Email not valid'], 400);
                        }
                        break;
                    case 'min':
                        if (strlen($data[$field]) < $value) {
                            Response::json(['message' => $field . ' field require min ' . $value . ' characters'], 400);
                        }
                        break;
                    case 'max':
                        if (strlen($data[$field]) > $value) {
                            Response::json(['message' => $field . ' field require max ' . $value . ' characters'], 400);
                        }
                        break;
                }
            }
        }
    }

    public static function isXhr() {
        if (! isset($_SERVER['CONTENT_TYPE'])) return false;
        if (strtolower($_SERVER['CONTENT_TYPE']) != 'application/json') return false;
        return true;
    }

    public static function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public static function isMethod($method) {
        return Request::method() == $method;
    }

    public static function method() {
        if (! isset($_SERVER['REQUEST_METHOD'])) return 'GET';
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                return 'GET';
            case 'POST':
                if ($_GET['_method'] == 'DELETE') {
                    #unset($_POST['_method']);
                    return 'DELETE';
                }
                if ($_GET['_method'] == 'PUT') {
                    #unset($_POST['_method']);
                    return 'PUT';
                }
                return 'POST';
            case 'PUT':
                return 'PUT';
            case 'DELETE':
                return 'DELETE';
        }
        return 'GET';
    }

    public static function isMobile() {
        if (isset($_GET['m']) && $_GET['m'] == 1) return true;
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", get_user_agent());
    }

    public static function get($key, $default = '') {
        if (! isset($_GET[$key])) return $default;
        return $_GET[$key];
    }
    public static function post($key, $default = '') {
        if (! isset($_POST[$key])) return $default;
        return $_POST[$key];
    }

    public static function cookie($key, $default = '') {
        if (! isset($_COOKIE[$key])) return $default;
        return $_COOKIE[$key];
    }
}