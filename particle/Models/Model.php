<?php
namespace Particle\Models;

use Particle\Database\MySQL;
use Particle\Database\MongoDB;

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
		$data = $class::checkFillable($data);
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

	public static function query($query, $singular = false) {
		$class = get_called_class();
		if ($class::$connection == 'mysql') {
			$response = MySQL::query($query);
		} elseif ($class::$connection == 'mongodb') {
			
		}
		if (empty($response)) return null;
		if (! empty($response) && is_array($response[0])) {
			$class = get_called_class();
			$items = [];
			foreach ($response as $item) {
				$items[] = new $class($item);
			}
		}
		if (! isset($items)) return $response;
		if ($singular) return $items[0];
		return $items;
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

	public static function paginate($limit = 20, $where = '1') {
		$page = isset($_GET['page'])?$_GET['page']:1;
		$class = get_called_class();
		$model = new $class;
		if (isset($_GET['s'])) {
			$where .= ' AND (';
			foreach ($class::$search as $key => $item) {
				if ($key != 0) {
					$where .= ' OR ';
				}
				$where .= $item . ' like \'%' . $_GET['s'] . '%\'';
			}
			$where .= ')';
		}
		$model->items = $model::query('SELECT * FROM ' . $model::$table . ' WHERE ' . $where . ' ORDER BY id DESC LIMIT ' . $limit . ' OFFSET ' . (($page - 1) * $limit));
		if (! $model->items) {
			$model->items = [];
		}
		$count = MySQL::selectOne('SELECT count(*) as total from ' . $model::$table . ' WHERE ' . $where .'');
		$model->count = $count['total'];
		$model->limit = $limit;
		$model->totalPages = intval($model->count/$limit);
		return $model;
	}

	public function links() {
		echo '<ul class="pagination">';
        echo '<li class="page-item disabled">';
        echo '<a class="page-link" href="#" aria-label="Previous">';
        echo '<span aria-hidden="true">«</span>';
        echo '</a>';
        echo '</li>';
        for ($i = 1; $i <= $this->totalPages; $i++) {
            echo '<li class="page-item"><a class="page-link active" href="?page=' . $i .'">' . $i . '</a></li>';
        }
        echo '<li class="page-item">';
        echo '<a class="page-link" href="#" aria-label="Next">';
        echo '<span aria-hidden="true">»</span>';
        echo '</a>';
        echo '</li>';
        echo '</ul>';
	}
}
