<?php
namespace Illumiate\Global\Interfaces;

interface IHelpers {
	public static function debugSetting($debug = false);
	public static function timeoutSetting();
	public static function gg($var, $die=true);
	public static function getOb($callback, $params = []);
}