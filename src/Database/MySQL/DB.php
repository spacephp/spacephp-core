<?php
namespace Illuminate\Database\MySQL;

class DB {
    private $mysqli;
    public $fetch;

  	private function __construct($host, $user, $password, $name)
  	{
        $this->mysqli = @new \mysqli($host, $user, $password, $name);
        if ($this->mysqli->connect_errno) {
            die("Failed to connect to MySQL: " . $this->mysqli->connect_error);
        }
  	}

    function __destruct()
    {
        $this->mysqli->close();
    }

    public static function connect() {
        global $mysql;
        if (! isset($mysql)) {
            $config = DB::getConfig();
            $mysql = new DB($config['host'], $config['user'], $config['password'], $config['database']);
        }
        return $mysql;
    }

    public static function getConfig() {
        if (defined('DB_HOST')) {
            return ['host' => DB_HOST, 'user' => DB_USER, 'password' => DB_PASSWORD, 'database' => DB_NAME];
        }
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/../.env')) {
            $env = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../.env');
            preg_match('/DB_HOST=(.*?)\n/', $env, $host);
            preg_match('/DB_DATABASE=(.*?)\n/', $env, $database);
            preg_match('/DB_USERNAME=(.*?)\n/', $env, $user);
            preg_match('/DB_PASSWORD=(.+?)\n/', $env, $password);
            return ['host' => trim($host[1]), 'user' => trim($user[1]), 'password' => trim(isset($password[1])?$password[1]:''), 'database' => trim($database[1])];
        }
        return ['host' => 'localhost', 'user' => 'root', 'password' => '', 'database' => 'myecom']; 
    }

    public static function find($table, $id) {
        $sql = 'SELECT * FROM ' . $table . ' WHERE id=' . $id;
        return DB::selectOne($sql);
    }

    public static function findOne($table, $filter) {
        $sql = 'SELECT * FROM `' . $table . '` WHERE ';
        $values = [];
        foreach ($filter as $key => $value) {
            $values[] = '`' . $key . '`' . '=' . DB::deletectValue($value);
        }
        $sql .= implode(', ', $values);
        $sql .= ' LIMIT 1';
        return DB::selectOne($sql);
    }

    public static function findAll($table, $filter, $limit = 10, $offset = 0) {
        $sql = 'SELECT * FROM `' . $table . '` WHERE ';
        $values = [];
        foreach ($filter as $key => $value) {
            $values[] = '`' . $key . '`' . '=' . DB::deletectValue($value);
        }
        $sql .= implode(', ', $values);
        $sql .= ' LIMIT ';
        if ($offset != 0) {
            $sql .= $offset . ', ';
        }
        $sql .= $limit;
        return DB::query($sql);
    }

    public static function all($table) {
        return DB::query('SELECT * FROM ' . $table);
    }

    public static function create($table, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = '`' . $key . '`';
            $values[] = DB::deletectValue($value);
        }
        $sql = 'INSERT INTO `' . $table . '` (' . implode(', ', $fields). ') VALUES (' . implode(', ', $values) . ')';
        return DB::query($sql);
    }

    public static function update($table, $data, $id) {
        $sql = 'UPDATE `' . $table . '` SET ';
        $values = [];
        foreach ($data as $key => $value) {
            $values[] = '`' . $key . '`' . '=' . DB::deletectValue($value);
        }
        $sql .= implode(', ', $values);
        $sql .= ' WHERE id=' . $id;
        return DB::query($sql);
    }

    public static function delete($table, $id) {
        $sql = 'DELETE FROM ' . $table . ' WHERE id=' . $id;
        return DB::query($sql);
    }

    public static function selectOne($query) {
        $result = DB::query($query);
        if (! empty($result) && is_array($result[0])) {
            return $result[0];
        }
        return $result;
    }

    public static function query($sql, $fetch = 'assoc')
    {
        $mysql = DB::connect();
        try {
            $mysqli_result = $mysql->mysqli->query($sql);
        } catch (\Exception $e) {
            return $mysql->mysqli->error . ' ' . $sql;
        }
        if ($mysqli_result === TRUE) {
            return ['rows_affected' => $mysql->mysqli->affected_rows, 'last_id' => $mysql->mysqli->insert_id];
        }
        if (is_object($mysqli_result) && get_class($mysqli_result) == 'mysqli_result') {
            $result = [];
            while ($row = ($fetch == 'row')?$mysqli_result->fetch_row():$mysqli_result->fetch_assoc()) {
                $result[] = $row;
            }
            $mysqli_result->free_result();
            return $result;
        }
    }

    private static function deletectValue($value) {
        if ($value === null) return 'null';
        if (is_numeric($value)) return $value;
        if (is_array($value)) return '"' . str_replace('"', '\"', json_encode($value)) . '"';
        return '"' . str_replace('"', '\"', $value) . '"';
    }
}