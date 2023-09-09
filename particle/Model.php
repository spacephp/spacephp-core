<?php
namespace Particle;

use Particle\MySQL;
use Particle\MongoDB;

class Model {
	public static $connection = 'mysql';
	public static $table;
	public static $collection;
	public static $fillable = ['id'];
	public static $timestamps = true;
	public static $softDelete = false;

    function __construct($data = []) {
		$data = $this->checkFillable($data);
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

	public static function create($data) {
		$class = get_called_class();
		$data = Model::checkFillable($data);
		if ($class::$connection == 'mysql') {
			$response = MySQL::insert($class::$table, $data);
		} elseif ($class::$connection == 'mongodb') {
			$response = MongoDB::insert($class::$collection, $data);
		}
		return $response;
	}

	public static function read($id) {
		$class = get_called_class();
		if ($class::$connection == 'mysql') {
			$response = MySQL::selectOne('SELECT * FROM ' . $class::$table . ' WHERE id=' . $id);
		} elseif ($class::$connection == 'mongodb') {
			$response = MongoDB::selectOne($class::$collection, $id);
		}
		return new $class($response);
	}

	public static function update($id, $data) {
		$class = get_called_class();
		$data = $class::checkFillable($data);
		if ($class::$connection == 'mysql') {
			$response = MySQL::update($class::$table, $id, $data);
		} elseif ($class::$connection == 'mongodb') {
			$response = MongoDB::update($class::$collection, $id, $data);
		}
		return $response;
	}

	public static function delete($id) {
		$class = get_called_class();
		if ($class::$connection == 'mysql') {
			$response = MySQL::delete($class::$table, $id);
		} elseif ($class::$connection == 'mongodb') {
			$response = MongoDB::delete($class::$collection, $id);
		}
		return $response;
	}

	public static function select($query) {
		$class = get_called_class();
		if ($class::$connection == 'mysql') {
			$response = MySQL::select($query);
		} elseif ($class::$connection == 'mongodb') {
			
		}
		return $response;
	}

	public static function selectOne($query) {
		$class = get_called_class();
		if ($class::$connection == 'mysql') {
			$response = MySQL::selectOne($query);
		} elseif ($class::$connection == 'mongodb') {
			
		}
		return $response;
	}

	public static function query($query) {
		$class = get_called_class();
		if ($class::$connection == 'mysql') {
			$response = MySQL::query($query);
		} elseif ($class::$connection == 'mongodb') {
			
		}
		return $response;
	}

	private static function checkFillable($data) {
		$class = get_called_class();
		foreach ($data as $key => $value) {
			if (! in_array($key, $class::$fillable)) {
				unset($data[$key]);
			}
		}
		return $data;
	}
}
