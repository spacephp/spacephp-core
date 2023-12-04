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
function get_ob($callback, $params = []){Helpers::getOb($callback, $params);}
// view
function _404(){View::_404();}
function goback(){View::goBack();}
function _view($name, $args = []){View::load($name, $args);}
function _view_partial($name, $args = []){View::partial($name, $args);}
// client
function get_user_ip(){Client::getUserIp();}
function get_user_agent(){Client::getUserAgent();}
function is_mobile(){Client::isMobile();}
// server
function get_protocol(){Server::getPrototol();}
function host_name() { Server::hostName();}
function site_url(){Server::siteUrl();}
// string
function is_json($str, $associative = null){Str::isJson($str, $associative);}
function slugify($text, string $divider = '-'){Str::slugify($text, $divider);}
function random_string($length = 10){Str::random($length);}
function get_string_between($str, $start, $end, $deep = 1){Str::getStringBetween($str, $start, $end, $deep);}
function get_strings_between($str, $start, $end){Str::getStringsBetween($str, $start, $end);}
function valid_url($url){Str::validUrl($url);}
// predecated
function __post($key, $default = ''){isset($_POST[$key]) ? $_POST[$key] : $default;}
function __cookie($key, $default = ''){isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;}
function __server($key, $default = ''){isset($_SERVER[$key]) ? $_SERVER[$key] : $default;}
function __get($key, $default = ''){isset($_GET[$key]) ? $_GET[$key] : $default;}
function __session($key, $default = ''){isset($_SESSION[$key]) ? $_SESSION[$key] : $default;}
