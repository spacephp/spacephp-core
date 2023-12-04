<?php
namespace Illuminate\Global\Interfaces;

interface IView {
	public static function load($name, $args = []));
	public static function _404();
	public static function goBack();
	public static function partial($name, $args = []);
}