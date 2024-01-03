<?php
namespace Illuminate\Database;

use Illuminate\Database\Interfaces\IDatabase;

class MongoDB extends MongoDBAbstract implements IDatabase {
    public function find($database, $collectionName, $id) {
        return $this->findWhere($database, $collectionName, ['_id' => $this->formatId($id)]);
    }

    public function findWhere($database, $collectionName, $filter) {
        $collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
		$doc = $collection->findOne($filter);
		if (! $doc) return null;
        $doc['_id'] = strval($doc['_id']);
		return (array) $doc;
    }

    public function paginate($database, $collectionName, $limit, $filter = [], $options = []) {
        $collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
        $limit = intval($limit);
		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$offset = ($page - 1) * $limit;
		$cursor = $collection->find($filter, array_merge(['skip' => $offset, 'limit' => $limit], $options));
		$count = $collection->countDocuments($filter);
		$docs = iterator_to_array($cursor);
        foreach ($docs as $key => $doc) {
            $docs[$key]['_id'] = strval($doc['_id']);
        }
		return [
			'docs' => $docs,
			'total' => $count,
			'page' => $page,
			'limit' => $limit,
			'totalPages' => ceil($count / $limit)
		];
    } 

    public function all($database, $collectionName, $filter = [], $options = []) {
        $collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
		$cursor = $collection->find($filter, $options);
		$docs = iterator_to_array($cursor);
        foreach ($docs as $key => $doc) {
            $docs[$key]['_id'] = strval($doc['_id']);
        }
		return $docs;
    } 

    public function findInIds($database, $collection, $ids, $options = []) {
        return $this->all(
            $database, 
            $collection, 
            ['_id' => 
                [
                    '$in' => array_map(function ($id) {return $this->formatId($id);}, $ids)
                ]
            ], 
            $options
        );
    }

    // action
    public function create($database, $collectionName, $data) {
		$collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
		$result = $collection->insertOne($data);
		$insertedId = $result->getInsertedId();
		return $this->find($database, $collectionName, $insertedId);
    }

    public function update($database, $collectionName, $id, $data) {
        $collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
        //$data = $this->encrypt($data);
        $updateResult = $collection->updateOne(
            ['_id' => $this->formatId($id)],
            ['$set' => $data]
        );
		return $this->find($database, $collectionName, $id);
    }

    public function delete($database, $collectionName, $id) {
		$collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
        $result = $collection->deleteOne(['_id' => $this->formatId($id)]);
		return $result->getDeletedCount();
    }

    public function deleteWhere($database, $collectionName, $filter) {
		$collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
        $result = $collection->deleteMany($filter);
		return $result->getDeletedCount();
    }

    // helper
	protected function formatId($id) {
		if ($id instanceof \MongoDB\BSON\ObjectID) {
            return $id;
        }
		try {
            return new \MongoDB\BSON\ObjectID($id);
        } catch (\Exception $e) {
            return $id;
        }
	}

	protected function isMongoId($value) {
        if ($value instanceof \MongoDB\BSON\ObjectID) {
            return true;
        }
        try {
            new \MongoDB\BSON\ObjectID($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
	}
}

abstract class MongoDBAbstract {
    protected $secret_key;
    protected $client = null;
    protected static $uris = [
        'default' => MONGO_URI,
        'sub' => MONGO_SUB
    ];

    protected function __construct($name = 'default') {
        $this->client = new \MongoDB\Client(MongoDBAbstract::$uris[$name] . "?retryWrites=true&w=majority");
        $this->secret_key = MONGO_SEED;
    }

    public static function setUris(array $uris) {
        MongoDBAbstract::$uris = $uris;
    }

    public static function connect($name = 'default') {
        global $mongodb;
        if (! isset($mongodb)) {
            $mongodb = [];
        }
        if (! isset($mongodb[$name])) {
            $class = get_called_class();
            $mongodb[$name] = new $class($name);
        }
        return $mongodb[$name];
    }
}