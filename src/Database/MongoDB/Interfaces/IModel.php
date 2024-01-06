<?php
namespace Illuminate\Database\MongoDB\Interfaces;

interface IModel {
    public static function find($id);
    public static function findWhere($filter);
    public static function paginate($limit, $filter = [], $options = []);
    public static function all($filter = [], $options = []);
    public static function create($data);
    public static function update($id, $data);
    public static function delete($id);
}