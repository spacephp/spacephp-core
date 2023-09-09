<?php
namespace Illuminate\Database\MongoDB;

use MongoDB\Client;

class DB {
    public $titles = [];
    public $documents = [];
    public static $client = null;

    public static function getClient() {
        if (! DB::$client) {
            if (defined('MONGO_URI')) {
                $uri = MONGO_URI . '?retryWrites=true&w=majority';
            } else {
                die('Mongo DB uri not set MONGO_URI');
            }
            DB::$client = new Client($uri);
        }
        return DB::$client;
    }

    public static function find($db, $collection, $id) {
        $client = DB::getClient();
        $database = $client->selectDatabase($db);
        $collection = $database->selectCollection($collection);
        if (is_array($id)) {
            $result = $collection->findOne($id);
        } else {
            $result = $collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        }
        if (! $result) return false;
        return $result;
    }

    public static function insert($db, $collection, $document) {
        $client = DB::getClient();
        $database = $client->selectDatabase($db);
        $collection = $database->selectCollection($collection);
        // Insert document
        $result = $collection->insertOne($document);
        // Check if the insert operation was successful
        if ($result->getInsertedCount() > 0) {
            return $result->getInsertedId();
        } else {
            return false;
        }
    }

    public static function update($db, $collection, $filter, $data, $options = []) {
        $client = DB::getClient();
        $database = $client->selectDatabase($db);
        $collection = $database->selectCollection($collection);
        // Insert document
        $options['returnDocument'] = \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER;
        if (is_array($filter)) {
            $result = $collection->findOneAndUpdate($filter, ['$set' => $data], $options);
        } else {
            $result = $collection->findOneAndUpdate(['_id' => new \MongoDB\BSON\ObjectId($filter)], ['$set' => $data], $options);
        }
        // Check if the insert operation was successful
        return $result;
    }

    public static function delete($db, $collection, $id) {
        $client = DB::getClient();
        $database = $client->selectDatabase($db);
        $collection = $database->selectCollection($collection);
        $deleteResult = $collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectID($id)]);
        // Check if the delete operation was successful
        if ($deleteResult->getDeletedCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get($db, $collection) {
        // Select database and collection
        $client = DB::getClient();
        $database = $client->selectDatabase($db);
        $collection = $database->selectCollection($collection);
        $perPage = 10; // Number of documents per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number

        // Fetch documents with pagination
        $skip = ($page - 1) * $perPage;
        $documents = $collection->find([], ['skip' => $skip, 'limit' => $perPage]);

        // Count total documents for pagination
        $totalDocuments = $collection->countDocuments();
        $this->totalPages = ceil($totalDocuments / $perPage);

        foreach ($documents as $document) {
            $this->documents[] = $document;
            foreach ($document as $key => $value) {
                if ($key == '_id') continue;
                $this->titles[] = $key;
            }
        }
        $this->titles = array_unique($this->titles);
    }
}