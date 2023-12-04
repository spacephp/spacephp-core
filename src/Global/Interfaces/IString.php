<?php
namespace Illumiate\Global\Interfaces;

interface IString {
	public static function getStringBetween($str, $start, $end, $deep = 1);
	public static function getStringsBetween($str, $start, $end);
	public static function slugify($text, string $divider = '-');
	public static function isJson($json);
	public static function validUrl($url);
	public static function random($length = 10);
}