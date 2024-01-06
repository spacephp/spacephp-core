<?php
namespace Illuminate\Database;

use Illuminate\Database\MySQL\DB;

class Migration {
    public static function run($table, $fields, $force = false, $engine="InnoDB", $chartset="utf8mb4") {
        if ($force) {
            DB::query('DROP TABLE ' . $table);
        }
        $sql = "CREATE TABLE IF NOT EXISTS $table (";
        $sql .= implode(',', $fields);
        $sql .= ") ENGINE=$engine DEFAULT CHARSET=$chartset";
        return DB::query($sql);
    }

    public static function back() {

    }
}