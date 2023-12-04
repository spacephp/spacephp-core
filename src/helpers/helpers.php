<?php
// general helper functions
use Illuminate\Global\Helpers;
use Illuminate\Global\View;
use Illuminate\Global\Str;
use Illuminate\Global\Client;
use Illuminate\Glocal\Server;

$debug_setting=fn($debug = false)=>Helpers::debugSetting($debug);
$timeout_setting=fn()=>Helpers::timeoutSetting();
$gg=fn($var, $die = true)=>Helpers::gg($var, $die);
$get_ob=fn($callback, $params = [])=>Helpers::getOb($callback, $params);
// view
$_404=fn()=>View::_404();
$goback=fn()=>View::goBack();
$_view=fn($name, $args = [])=>View::load($name, $args);
$_view_partial=fn($name, $args = [])=>View::partial($name, $args);
// client
$get_user_ip=fn()=>Client::getUserIp();
$get_user_agent=fn()=>Client::getUserAgent();
$is_mobile=fn()=>Client::isMobile();
// server
$get_protocol=fn()=>Server::getPrototol();
$host_name=fn()=>Server::hostName();
$site_url=fn()=>Server::siteUrl();
// string
$is_json=fn($str, $associative = null)=>Str::isJson($str, $associative);
$slugify=fn($text, string $divider = '-')=>Str::slugify($text, $divider);
$random_string=fn($length = 10)=>Str::random($length);
$get_string_between=fn($str, $start, $end, $deep = 1)=>Str::getStringBetween($str, $start, $end, $deep);
$get_strings_between=fn($str, $start, $end)=>Str::getStringsBetween($str, $start, $end);
$valid_url=fn($url)=>Str::validUrl($url);
// predecated
$__post=fn($key, $default = '')=>isset($_POST[$key]) ? $_POST[$key] : $default;
$__cookie=fn($key, $default = '')=>isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
$__server=fn($key, $default = '')=>isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
$__get=fn($key, $default = '')=>isset($_GET[$key]) ? $_GET[$key] : $default;
$__session=fn($key, $default = '')=>isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
