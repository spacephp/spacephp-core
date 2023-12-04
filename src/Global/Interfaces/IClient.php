<?php
namespace Illuminate\Global\Interfaces;

interface IClient {
	public static function getUserIp();
	public static function getUserAgent();
	public static function isMobile();
}