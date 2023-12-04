<?php
namespace Illuminate\Global\Interfaces;

interface IServer {
	public static function getProtocol();
	public static function hostName();
	public static function siteUrl();
}