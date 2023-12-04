<?php
namespace Illuminate\Global;

use Illumiate\Global\Interfaces\IHelpers;

class Helpers implements IHelpers {
	public static function debugSetting($debug = false) {
		if ($debug || isset($_GET['debug'])) {
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1');
			error_reporting(E_ALL);
		}
	}

	public static function timeoutSetting() {
		ini_set('memory_limit', '-1');
		set_time_limit(0);
	}

	public static function gg($var, $die=true) {
		echo '<pre>';
		print_r($var);
		echo '</pre>';
		if ($die) {
			die();
		}
	}

	public static function getOb($callback, $params = []) {
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
}