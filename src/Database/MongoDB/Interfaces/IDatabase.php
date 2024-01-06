<?php
namespace Illuminate\Database\MongoDB\Interfaces;

interface IDatabase {
    public function find($database, $collectionName, $id);
    public function findWhere($database, $collectionName, $filter);
    public function paginate($database, $collectionName, $limit, $filter = [], $options = []);
    public function all($database, $collectionName, $filter = [], $options = []);
    public function create($database, $collectionName, $data);
    public function update($database, $collectionName, $id, $data);
    public function delete($database, $collectionName, $id);
}