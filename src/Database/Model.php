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

	public static function read($id) {
		$class = get_called_class();
		if (! is_numeric($id)) {
			$response = DB::selectOne($id);
		} else {
			$response = DB::selectOne('SELECT * FROM ' . $class::$table . ' WHERE id=' . $id);
		}
		if (empty($response)) return null;
		return new $class($response);
	}

	public static function readMultiple($query) {
		$class = get_called_class();
		$response = DB::select($query);
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

	public static function create($data, $objResponse = false) {
		$class = get_called_class();
		if (is_string($data)) {
			$response = DB::query($data);
		} else {
			$data = $class::checkFillable($data);
			$response = DB::insert($class::$table, $data);
		}
		if ($objResponse) {
			return $class::read($response['insert_id']);
		}
		return $response;
	}

	public static function update($id, $data = [], $objResponse = false) {
		if (! is_numeric($id)) return DB::query($id);	
		$class = get_called_class();
		$data = $class::checkFillable($data);
		$response = DB::update($class::$table, $id, $data);
		if ($objResponse) {
			return $class::read($id);
		}
		return $response;
	}

	public static function delete($id) {
		if (! is_numeric($id)) return DB::query($id);
		$class = get_called_class();
		return DB::delete($class::$table, $id);
	}

	public static function query($query) {
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

	public static function paginate($options, $limit = 20) {
		$select = isset($options['select'])?$options['select']:'*';
		$where = isset($options['where'])?$options['where']:'1';
		$page = isset($_GET['page'])?$_GET['page']:1;
		$search = isset($_GET['s'])?$_GET['s']:false;
		$order = isset($options['order'])?$option['order']:['field' => 'id', 'type' => 'desc'];
		$class = get_called_class();
		$model = new $class;
		if ($search) {
			$where .= ' AND (';
			foreach ($class::$search as $key => $item) {
				if ($key != 0) {
					$where .= ' OR ';
				}
				$where .= $item . ' like \'%' . $_GET['s'] . '%\'';
			}
			$where .= ')';
		}
		$model->items = $model::query('SELECT ' . $select . ' FROM ' . $model::$table . ' WHERE ' . $where . ' ORDER BY ' . $order['field'] . ' ' . $order['type'] . ' LIMIT ' . $limit . ' OFFSET ' . (($page - 1) * $limit));
		if (! $model->items) {
			$model->items = [];
		}
		$count = DB::selectOne('SELECT count(*) as total from ' . $model::$table . ' WHERE ' . $where .'', true);
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
        for ($i = 0; $i <= $this->totalPages; $i++) {
            echo '<li class="page-item"><a class="page-link active" href="?page=' . ($i + 1) .'">' . ($i + 1) . '</a></li>';
        }
        echo '<li class="page-item">';
        echo '<a class="page-link" href="#" aria-label="Next">';
        echo '<span aria-hidden="true">»</span>';
        echo '</a>';
        echo '</li>';
        echo '</ul>';
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
	public static function paginate($options, $limit = 20);
	public function links();
}