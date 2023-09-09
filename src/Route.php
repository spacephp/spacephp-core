<?php
namespace Illuminate;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Route {
    public static $uri = null;
    public static $fullUri = '';
    public static $params = [];
    public static $namespace = '';

    public static function get($path, $controller) {if (! Request::isMethod('GET')) return;Route::run($path, $controller);}
    public static function post($path, $controller) {if (! Request::isMethod('POST')) return;Route::run($path, $controller);}
    public static function put($path, $controller) {if (! Request::isMethod('PUT')) return;Route::run($path, $controller);}
    public static function delete($path, $controller) {if (! Request::isMethod('DELETE')) return;Route::run($path, $controller);}

    public static function resource($name, $class, $option = ['only' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']]) {
        $routes = [
            'index' => ['get', '/' . $name],
            'create' => ['get', '/' . $name . '/create'],
            'store' => ['post', '/' . $name],
            'show' => ['get', '/' . $name . '/{id}'],
            'edit' => ['get', '/' . $name . '/{id}/edit'],
            'update' => ['put', '/' . $name . '/{id}'],
            'destroy' => ['delete', '/' . $name . '/{id}']
        ];
        if (isset($option['only'])) {
            foreach ($option['only'] as $method) {
                Route::{$routes[$method][0]}($routes[$method][1], [$class, $method]);
            }
        }
        if (isset($option['except'])) {
            foreach ($routes as $method => $resource) {
                if (! in_array($method, $option['except'])) {
                    Route::{$resource[0]}($resource[1], [$class, $method]);
                }
            }
        }
    }
    public static function group($params, $controller) {
        $uri = Route::parseUri();
        $preNamespace = Route::$namespace;
        $namespace = $preNamespace . (isset($params['namespace'])?$params['namespace']:'');
        $type = isset($params['type'])?$params['type']:'web';
        if (strpos(Route::$fullUri, $namespace) !== 0) return;
        if ($type == 'api' && ! Request::isXhr()) return;
        Route::$namespace = $namespace;
        $controller();
        Route::$namespace = $preNamespace;
    }

    public static function domain($domain, $controller) {
        if ($_SERVER['HTTP_HOST'] != $domain) {
            return false;
        }
        $controller();
    }

    public static function redirect($url) {
        header("Location: $url");
        die();
    }

    public static function end($controller = null) {
        if ($controller) {
            Route::runController($controller);
        }
        Route::runController(function () {
            return view('404');
        });
    }

    private static function run($path, $controller) {
        if (! Route::uriMatch(Route::$namespace . $path)) return;
        Route::runController($controller);
    }

    protected static function runController($controller) {
        if (isset($_POST['_method'])) unset($_POST['_method']);
        if (is_array($controller)) {
            $obj = new $controller[0];
            $response = call_user_func_array([$obj, $controller[1]], Route::$params);
        } else {
            $response = call_user_func_array($controller, Route::$params);
        }
        if (is_array($response)) {
            Response::json($response);
        }
        if (is_object($response)) {
            Response::json($response);
        }
        #echo $response;
        die();
    }

    private static function uriMatch($path) {
        if (! $path) return false;
        if (! Route::$uri) {
            Route::parseUri();
        }
        if ($path == Route::$fullUri) return true;
        $path = array_filter(explode('/', $path));
        if (empty($path) || ! $path) return false;
        if (! Route::$uri) return false;
        if (count($path) != count(Route::$uri)) return false;
        Route::$params = [];
        foreach ($path as $key => $child) {
            $match = null;
            if ($child == Route::$uri[$key] || preg_match('/\{(.*?)\}/', $child, $match)) {
                if ($match) {
                    Route::$params[] = Route::$uri[$key];
                }
                continue;
            }
            return false;
        }
        return true;
    }

    private static function parseUri() {
        if (! Route::$uri) {
            if (! isset($_SERVER['REQUEST_URI'])) return '/';
            Route::$fullUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
            Route::$uri = array_filter(explode('/', Route::$fullUri));
        }
        return Route::$uri;
    }
}