<?php
namespace MS\Controllers;

use Illuminate\Database\DB;
use Illuminate\Http\Request;

class ApiController {
    public function index($table) {
        return DB::table($table)->all();
    }

    public function store($table) {
        $data = Request::json();
        return DB::create($table, $data);
    }

    public function show($table, $id) {
        return DB::findById($table, $id);
    }

    public function update($table, $id) {
        $data = Request::json();
        return DB::update($table, $id, $data);
    }

    public function destroy($table, $id) {
        return DB::destroy($table, $id);
    }
}