<?php
namespace Illuminate\Database\MongoDB;

class Model {
    public static $database = 'microservices';
    public static $cache = false;

    function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
        if (isset($data['_id'])) {
            $this->_id = strval($data['_id']);
        }
    }

    public function getKey($key, $default = '') {
        if (! isset($this->{$key}) || ! $this->{$key}) return $default;
        return $this->{$key};
    }

    public static function has($key) {
        $model = get_called_class();
        if (in_array($key, $model::$fields)) return true;
        return false;
    }

    public static function find($filter, $cache = true) {
        $class = get_called_class();
        if ($class::$cache && $cache) {
            $file = $class::generateCacheFile($filter);
            if (! file_exists(CACHE_DIR . $file) || isset($_GET['cache'])) {
                $result = DB::find($class::$database, $class::$collection, $filter);
                if (isset($result['_id'])) {
                    $result['_id'] = strval($result['_id']);
                }
                file_put_contents(CACHE_DIR . $file, json_encode($result));
            }
            $result = json_decode(file_get_contents(CACHE_DIR . $file), true);
        } else {
            $result = DB::find($class::$database, $class::$collection, $filter);
        }
        if (! $result) return false;
        return new $class($result);
    }

    public static function insert($data) {
        $class = get_called_class();
        if ($class::has('host')) {
            $data['host'] = host_name();
        }
        if ($class::has('ip')) {
            $data['ip'] = get_user_ip();
        }
        if ($class::has('user_agent')) {
            $data['user_agent'] = get_user_agent();
        }
        if ($class::has('post_id')) {
            $data['post_id'] = md5('Illuminate_' . __post('url'));
        }
        unset($data['_method']);
        unset($data['files']);
        $result = DB::insert($class::$database, $class::$collection, $data);
        if (! $result) return false;
        return strval($result);
    }

    public static function update($filter, $data, $option = []) {
        $class = get_called_class();
        $result = DB::update($class::$database, $class::$collection, $filter, $data, $option);
        if (! $result) return false;
        if ($class::$cache) {
            file_get_contents(site_url() . '?cache=true');
        }
        return new $class($result);
    }

    public static function destroy($id) {
        $class = get_called_class();
        return DB::delete($class::$database, $class::$collection, $id);
    }

    public static function get($perPage = 10, $filter = []) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
        // Fetch documents with pagination
        $skip = ($page - 1) * $perPage;
        $class = get_called_class();
        $client = DB::getClient();
        $database = $client->selectDatabase($class::$database);
        $collection = $database->selectCollection($class::$collection);
        $obj = new $class;
        if ($class::has('host')) {
            $filter['host'] = host_name();
        }
        $documents = $collection->find($filter, ['skip' => $skip, 'limit' => $perPage, 'sort' => ['_id' => -1]]);
        // Count total documents for paginatio
        $totalDocuments = $collection->countDocuments();
        $obj->totalPages = ceil($totalDocuments / $perPage);
        $obj->documents = [];
        foreach ($documents as $doc) {
            $obj->documents[] = new $class($doc);
        }
        $obj->totalDocuments = count($obj->documents);
        return $obj;
    }

    public function list() {
        echo "<table>";
        echo "<tr>";
        foreach ($this->titles as $title) {
            echo "<th>" . ucwords($title) . "</th>";
        }
        echo "<th>Actions</th>";
        echo "</tr>";
        foreach ($this->documents as $document) {
            echo "<tr>";
            foreach ($this->titles as $title) {
                if (! isset($document[$title])) {
                    echo '<td>-</td>';
                } else {
                    echo "<td>" . $document[$title] . "</td>";
                }
            }
            echo "<td><a href='?delete=" . $document['_id'] . "'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</table>";
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

    public function title() {
        $class = get_called_class();
        return ucwords($class::$collection);
    }

    public static function generateCacheFile($filter) {
        $class = get_called_class();
        $name = $class::$database . $class::$collection;
        if (!is_array($filter)) {
            $name .= $filter;
        } else {
            foreach ($filter as $key => $value) {
                $name .= $key . $value;
            }
        }
        return $name . '.json';
    }
}