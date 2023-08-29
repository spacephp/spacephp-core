<?php
namespace Illuminate\Database;

class Migration {
    public static function run($table, $fields, $engine="InnoDB", $chartset="utf8mb4") {
        $sql = "CREATE TABLE IF NOT EXISTS $table (";
        $sql .= implode(',', $fields);
        $sql .= ") ENGINE=$engine DEFAULT CHARSET=$chartset";
        return DB::query($sql);
    }

    public static function back() {

    }
}