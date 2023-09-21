<?php
namespace Illuminate\Database;

use Illuminate\Database\MySQL\DB;

class Model implements IModel{
	public static $table;
	public static $fillable = ['id'];
	public static $timestamps = true;
	public static $softDelete = false;

    function __construct($data = []) {
		$data = $this->checkFillable($data);
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

	public static function create($data, $objectResponse = false) {
		$class = get_called_class();
		if (is_string($data)) {
			$response = DB::query($data);
		} else {
			$data = $class::checkFillable($data);
			$response = DB::insert($class::getTableName(), $data);
		}
		if ($objectResponse) {
			return $class::read($response['insert_id']);
		}
		return $response;
	}

	public static function read($id) {
		$class = get_called_class();
		if (is_string($id)) {
			$response = DB::selectOne($id);
		} else {
			$response = DB::selectOne('SELECT * FROM ' . $class::getTableName() . ' WHERE id=' . $id);
		}
		return new $class($response);
	}

	public static function readMultiple($query) {
		$class = get_called_class();
		$response = DB::query($query);
		if (empty($response)) return null;
		if (! empty($response) && is_array($response[0])) {
			$class = get_called_class();
			$items = [];
			foreach ($response as $item) {
				$items[] = new $class($item);
			}
		}
		if (! isset($items)) return $response;
		return $items;
	}

	public static function update($id, $data = [], $objectResponse = false) {
		if (is_string($id)) {
			$response = DB::query($id);
		} else {
			$class = get_called_class();
			$data = $class::checkFillable($data);
			$response = DB::update($class::getTableName(), $id, $data);
			if ($objectResponse) {
				return $class::read($id);
			}
		}
		return $response;
	}

	public static function delete($id) {
		if (is_string($id)) {
			$response = DB::query($id);
		} else {
			$class = get_called_class();
			$response = DB::delete($class::getTableName(), $id);
		}
		return $response;
	}

	public static function query($query, $singular = false) {
		$class = get_called_class();
		$response = DB::query($query);
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
		$model->items = $model::query('SELECT * FROM ' . $model::getTableName() . ' WHERE ' . $where . ' ORDER BY id DESC LIMIT ' . $limit . ' OFFSET ' . (($page - 1) * $limit));
		if (! $model->items) {
			$model->items = [];
		}
		$count = DB::selectOne('SELECT count(*) as total from ' . $model::getTableName() . ' WHERE ' . $where .'');
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

	private static function getTableName() {
		$class = get_called_class();
		if ($class::$table) return $class::$table;
		$class::$table = strtolower($class) . 's';
		return $class::$table;
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

interface IModel {
	public static function read($id);
	public static function readMultiple($query);
	public static function create($data, $objectResponse = false);
	public static function update($id, $data = [], $objectResponse = false);
	public static function delete($id);
}