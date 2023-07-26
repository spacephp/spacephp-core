<?php
// general helper functions
function debug_setting($debug = false)
{
    if ($debug || Illuminate\Request::get('debug')) {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    }
}

function timeout_setting()
{
    ini_set('memory_limit', '-1');
    set_time_limit(0);
}

function gg($var, $die=true)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($die) {
        die();
    }
}

function get_ob($callback, $params = []) {
    ob_start();
    if (is_array($callback)) {
        $controller = new $callback[0];
        $action = $callback[1];
        $response = call_user_func_array([$controller, $action], $params);
    } else {
        $response = call_user_func_array($callback, $params);
    }
    echo $response;
    return ob_get_clean();
}

function goback() {
    header('Location: ' . Illuminate\Server::get('HTTP_REFERER'));
    die();
}

function _404() {
    die('404 <a href="/">Go back</a>');
}

function _view($name, $args = []) {
    if (! defined('VIEW_FOLDER')) {
        define('VIEW_FOLDER', Illuminate\Server::get(DOCUMENT_ROOT . '/views'));
    }
    foreach ($args as $key => $value) {
        ${$key} = $value;
    }
    include(VIEW_FOLDER . '/' . $name . '.php');
}

function _view_partial($name, $args = []) {
    if (! defined('VIEW_FOLDER')) {
        define('VIEW_FOLDER', Illuminate\Server::get(DOCUMENT_ROOT . '/views'));
    }
    foreach ($args as $key => $value) {
        ${$key} = $value;
    }
    include(VIEW_FOLDER . '/' . $name . '.php');
}

function admin_view($name, $args = []) {
    foreach ($args as $key => $value) {
        ${$key} = $value;
    }
    include(ADMIN_VIEW . '/' . $name . '.php');
    die();
}

function admin() {
    $admin = include(Illuminate\Server::get('DOCUMENT_ROOT') . '/../config/admin.php');
    return $admin;
}