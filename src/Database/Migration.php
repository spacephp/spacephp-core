<?php
namespace Illuminate\Database;

class Migration {
    public static function run() {
        $fields = [
            'id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT',
            'email VARCHAR(64) NOT NULL',
            'PRIMARY KEY (`id`)',
            'INDEX `email_index` (`email`)'
        ];
        $sql = 'CREATE TABLE `xptracker`.`test` (';
        $sql .= implode(',', $fields);
        $sql .= ');';
    }

    public static function back() {

    }
}