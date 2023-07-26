<?php
require('vendor/autoload.php');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'xptracker');
use Illuminate\Database\MySQL\DB;
gg(DB::findById('users', 10));