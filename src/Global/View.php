<?php
namespace Illuminate\Global;

use Illumiate\Global\Interfaces\IView;

class View implements IView {
	public static function load($name, $args = [])) {
		if (! defined('VIEW_FOLDER')) {
			define('VIEW_FOLDER', $_SERVER['DOCUMENT_ROOT'] . '/../views');
		}
		foreach ($args as $key => $value) {
			${$key} = $value;
		}
		include(VIEW_FOLDER . '/' . $name . '.php');
	}

	public static function _404() {
		die('404 <a href="/">Go back</a>');
	}

	public static function goBack() {
		$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		header('Location: ' . $ref);
		die();
	}

	public static function partial($name, $args = []) {
		if (! defined('VIEW_FOLDER')) {
			define('VIEW_FOLDER', $_SERVER['DOCUMENT_ROOT'] . '/../views');
		}
		foreach ($args as $key => $value) {
			${$key} = $value;
		}
		include(VIEW_FOLDER . '/' . $name . '.php');
	}
}