<?php
namespace Particle;

class MySQL {
	private $mysqli;

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
            $mysql = new MySQL(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        }
        return $mysql;
    }

    public static function select($query) {
        return MySQL::query($query);
    }  

    public static function selectOne($query) {
        $result = MySQL::query($query);
        if (! empty($result) && is_array($result[0])) {
            return $result[0];
        }
        return $result;
    }

    public static function insert($table, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = '`' . $key . '`';
            $values[] = MySQL::deletectValue($value);
        }
        $sql = 'INSERT INTO `' . $table . '` (' . implode(', ', $fields). ') VALUES (' . implode(', ', $values) . ')';
        return MySQL::query($sql);
    }

    public static function update($table, $data, $id) {
        $sql = 'UPDATE `' . $table . '` SET ';
        $values = [];
        foreach ($data as $key => $value) {
            $values[] = '`' . $key . '`' . '=' . MySQL::deletectValue($value);
        }
        $sql .= implode(', ', $values);
        $sql .= ' WHERE id=' . $id;
        return MySQL::query($sql);
    }

    public static function delete($table, $id) {
        $sql = 'DELETE FROM ' . $table . ' WHERE id=' . $id;
        return MySQL::query($sql);
    }

    public static function query($sql, $fetch = 'assoc')
    {
        $mysql = MySQL::connect();
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