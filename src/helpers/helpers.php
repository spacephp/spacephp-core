<?php
// general helper functions
use Illuminate\Global\Helpers;
use Illuminate\Global\View;
use Illuminate\Global\Str;
use Illuminate\Global\Client;
use Illuminate\Global\Server;

function debug_setting($debug = false){Helpers::debugSetting($debug);}
function timeout_setting(){Helpers::timeoutSetting();}
function gg($var, $die = true){Helpers::gg($var, $die);}
function get_ob($callback, $params = []){return Helpers::getOb($callback, $params);}
// view
function _404(){View::_404();}
function goback(){View::goBack();}
function _view($name, $args = []){View::load($name, $args);}
function _view_partial($name, $args = []){View::partial($name, $args);}
// client
function get_user_ip(){return Client::getUserIp();}
function get_user_agent(){return Client::getUserAgent();}
function is_mobile(){return Client::isMobile();}
// server
function get_protocol(){return Server::getPrototol();}
function host_name() {return Server::hostName();}
function site_url(){return Server::siteUrl();}
// string
function is_json($str, $associative = null){return Str::isJson($str, $associative);}
function slugify($text, string $divider = '-'){return Str::slugify($text, $divider);}
function random_string($length = 10){return Str::random($length);}
function get_string_between($str, $start, $end, $deep = 1){return Str::getStringBetween($str, $start, $end, $deep);}
function get_strings_between($str, $start, $end){return Str::getStringsBetween($str, $start, $end);}
function valid_url($url){return Str::validUrl($url);}
function base31_encode($str){return Str::base31Encode($str);}
// predecated
function __post($key, $default = ''){return isset($_POST[$key]) ? $_POST[$key] : $default;}
function __cookie($key, $default = ''){return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;}
function __server($key, $default = ''){return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;}
function __get($key, $default = ''){return isset($_GET[$key]) ? $_GET[$key] : $default;}
function __session($key, $default = ''){return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;}
