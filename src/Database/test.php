<?php
echo "\033[33m --- MongoDB Test --- \033[0m\n";
$data = json_decode(file_get_contents($url . '/mongodb/test'), true);
if ($data[0]['email'] == 'nhat@gmail.com' && $data[1]['email'] == 'nhatupdate@gmail.com' && $data[2] == 1) {
    echo "\033[32mOK - MongoDB CUD success - " . $id . "\033[0m\n";
} else {
    echo "\033[31mFail - MongoDB CUD fail \033[0m\n";
}