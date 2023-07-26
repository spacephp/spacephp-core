<?php
namespace MS\Models;

use Illuminate\MongoDB\Model;

class Visitor extends Model {
    public static $fields = ['location'];
    public static $collection = 'visitors';

    public static function current() {
        $ip = get_user_ip();
        $visitor = Visitor::find($ip);
        if (! $visitor) {
            return Visitor::insert([
                '_id' => $ip,
                'location' => Visitor::getLocation($ip)
            ]);
        }
        return $visitor['_id'];        
    }

    private static function getLocation($ip) {
        $location = @file_get_contents('http://ipwho.is/' . $ip);
        if (! $location) return '.';
        $location = json_decode($location, true);
        if (isset($location['country_code'])) {
            return $location['country_code'];
        }
        return '.';
    }
}