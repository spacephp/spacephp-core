<?php
namespace Illuminate\Database\Interface;

interface IModel {
	public static function create($data, $objectResponse = false);
	public static function read($id);
	public static function readMultiple($query);
	public static function update($id, $data = [], $objectResponse = false);
	public static function delete($id);
}