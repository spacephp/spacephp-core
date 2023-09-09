<?php
namespace Illuminate\Database;

interface Model {
	public static function create($data);
	public static function read($id);
	public static function update($id, $data);
	public static function delete($id);
}