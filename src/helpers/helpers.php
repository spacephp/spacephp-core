<?php
// general helper functions
function debug_setting($debug = false)
{
    if ($debug || Illuminate\Http\Request::get('debug')) {
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
    header('Location: ' . __server('HTTP_REFERER', '/'));
    die();
}

function _404() {
    die('404 <a href="/">Go back</a>');
}

function _view($name, $args = []) {
    if (! defined('VIEW_FOLDER')) {
        define('VIEW_FOLDER', __server('DOCUMENT_ROOT') . '/../views');
    }
    foreach ($args as $key => $value) {
        ${$key} = $value;
    }
    include(VIEW_FOLDER . '/' . $name . '.php');
}

function _view_partial($name, $args = []) {
    if (! defined('VIEW_FOLDER')) {
        define('VIEW_FOLDER', __server('DOCUMENT_ROOT') . '/../views');
    }
    foreach ($args as $key => $value) {
        ${$key} = $value;
    }
    include(VIEW_FOLDER . '/' . $name . '.php');
}

// client
function get_user_ip() {
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = @$_SERVER['REMOTE_ADDR'];

    if(filter_var( $client, FILTER_VALIDATE_IP ))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

function get_user_agent() {
    return __server("HTTP_USER_AGENT");
}

function is_mobile() {
    if (__get('m') == 1) return true;
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", get_user_agent());
}

function __cookie($key, $default = '')
{
    return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
}

function __server($key, $default = '')
{
    return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
}

function __get($key, $default = '') {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

function __session($key, $default = '') {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}

function __post($key, $default = '') {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

// server
function get_protocol() {
    if (isset($_SERVER['HTTPS'])
        && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    ) {
            return 'https';
    }

    return 'http';
}

function host_name() {
    return __server('HTTP_HOST', '127.0.0.1');
}

function site_url() {
    return get_protocol() . '://' . host_name();
}

// string
function is_json($string) {
    if (is_numeric($string)) return false;
    $response = json_decode($string);
    if (! $response) return false;
    return json_last_error() === JSON_ERROR_NONE;
}

function slugify($text, string $divider = '-')
{
    // replace non letter or digits by divider
    if (! $text) {
        return false;
    }

    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
    if (! $text) {
        return false;
    }
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, $divider);
    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);
    // lowercase
    $text = strtolower($text);
    if (! $text) {
        return false;
    }
    return $text;
}

function random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function get_string_between($str, $start, $end, $deep = 1)
{
    $str = explode($start, $str);
    if (! isset($str[$deep])) {
        return '';
    }
    $str = explode($end, $str[$deep]);
    if (! isset($str[1])) return '';
    return $str[0];
}

function get_strings_between($str, $start, $end) {
    $matches = [];
    $pattern = '/' . preg_quote($start, '/') . '(.*?)' . preg_quote($end, '/') . '/';
    preg_match_all($pattern, $str, $matches);

    return $matches[1]; // Return the captured strings
}

function valid_url($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== FALSE;
}

function minimize_css($css){
    $css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css); // negative look ahead
    $css = preg_replace('/\s{2,}/', ' ', $css);
    $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
    $css = preg_replace('/;}/', '}', $css);
    $css = preg_replace('/,\s/', ',', $css);
    return $css;
}