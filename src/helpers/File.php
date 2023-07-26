<?php
namespace Illuminate;

class File {
    public static function save($file, $content, $mode = 0777) {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, $mode, true);
        }
        file_put_contents($file, $content);
    }
    
    public static function get($file, $default = '') {
        $content = @file_get_contents($file);
        if (! $content) {
            return $default;
        }
        return $content;
    }
}