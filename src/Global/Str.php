<?php
namespace Illuminate\Global;

use Illuminate\Global\Interfaces\IString;

class Str implements IString {
	public static function getStringBetween($str, $start, $end, $deep = 1) {
        $str = explode($start, $str);
        if (! isset($str[$deep])) {
            return '';
        }
        $str = explode($end, $str[$deep]);
        if (! isset($str[1])) return '';
        return $str[0];
	}

	public static function getStringsBetween($str, $start, $end) {
        $matches = explode($start, $str);
        $result = [];
        for ($i = 1; $i < count($matches); ++$i) {
            $match = explode($end, $matches[$i]);
            $result[] = $match[0];
        }
        return $result;
	}

	public static function slugify($text, string $divider = '-') {
		if (! $text) {
            return '';
        }

        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
        if (! $text) {
            return '';
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
            return '';
        }
        return $text;
	}

	public static function isJson($str, $associative = null) {
		if (is_numeric($str)) return false;
		$response = json_decode($str, $associative);
		if (! $response) return false;
		if (json_last_error() !== JSON_ERROR_NONE) return false;
		return $response;
	}

	public static function validUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== FALSE;
	}

	public static function random($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
	}
    
    public static function base31Encode($str) {
        $md5Hash = md5($str);
        $value = base_convert($md5Hash, 16, 31);
        return $value;
    }
}