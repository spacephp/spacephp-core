<?php
namespace MS\Models;

use Illuminate\Database\MongoDB\Model;

class View extends Model {
    public static $fields = ['visitor_id', 'user_agent', 'host', 'page', 'is_mobile', 'time', 'referrer'];
    public static $collection = 'views';

    public static function track() {
        $visitor_id = Visitor::current();
        $view = View::insert([
            'visitor_id' => $visitor_id,
            'user_agent' => get_user_agent(),
            'host' => host_name(),
            'page' => site_url() . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
            'is_mobile' => is_mobile(),
            'time' => 0,
            'referrer' => __server('HTTP_REFERER')
        ]);
        return $view;
    }

    private static function getPage($link = '') {
        if (! $link) $link = $_SERVER['HTTP_REFERER'];
        return strtok($link, '#');
    }
}