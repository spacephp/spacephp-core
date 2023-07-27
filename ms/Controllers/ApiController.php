<?php
namespace MS\Controllers;

use Illuminate\Database\MySQL\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiController {
    public function index($table) {
        $result = DB::all($table);
        return $result;
    }

    public function store($table) {
        $result = DB::create($table, Request::json());
        if (is_string($result)) {
            return Response::json(['error' => true, 'message' => $result], 500);
        }
        if (! isset($result['last_id'])) {
            return Response::json(['error' => true, 'message' => 'Unknown error', 'data' => $result], 500);
        }
        return DB::find($table, $result['last_id']);
    }

    public function show($table, $id) {
        $result = DB::find($table, $id);
        if (empty($result)) {
            return Response::json(['error' => true, 'message' => 'not found'], 404);
        }
        return $result;
    }

    public function update($table, $id) {
        $result = DB::update($table, Request::json(), $id);
        if (is_string($result)) {
            return Response::json(['error' => true, 'message' => $result], 500);
        }
        return DB::find($table, $id);
    }

    public function destroy($table, $id) {
        $result = DB::delete($table, $id);
        if (! isset($result['rows_affected'])) {
            return Response::json(['error' => true, 'message' => 'Unknown error', 'data' => $result], 500);
        }
        return Response::json(['success' => true]);
    }
}