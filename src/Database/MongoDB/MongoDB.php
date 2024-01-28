<?php
namespace Illuminate\Database\MongoDB;

use Illuminate\Database\MongoDB\Interfaces\IDatabase;

class MongoDB extends MongoDBAbstract implements IDatabase {
    use MongoDBInteractionTrait;
}

trait MongoDBInteractionTrait {
    public function find($database, $collectionName, $id) {
        return $this->findWhere($database, $collectionName, ['_id' => MongoDB::formatId($id)]);
    }

    public function findWhere($database, $collectionName, $filter) {
        $collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
		$doc = $collection->findOne($filter);
		if (! $doc) return null;
        $doc['_id'] = strval($doc['_id']);
		return MongoDB::parseArray($doc);
    }

    public function count($database, $collectionName, $filter = []) {
        $collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
        $count = $collection->countDocuments($filter);
        return $count;
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
            $docs[$key] = MongoDB::parseArray($docs[$key]);
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
            $docs[$key] = MongoDB::parseArray($docs[$key]);
        }
		return $docs;
    } 

    public function findInIds($database, $collection, $ids, $options = []) {
        return $this->all(
            $database, 
            $collection, 
            ['_id' => 
                [
                    '$in' => array_map(function ($id) {return MongoDB::formatId($id);}, $ids)
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

    public function createMany($database, $collectionName, $data) {
        $collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
		$result = $collection->insertMany($data);
		return $result;
    }

    public function replace($database, $collectionName, $filter, $data) {
        $collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
		$result = $collection->replaceOne($filter, $data);
		return $this->find($database, $collectionName, $insertedId);
    }

    public function update($database, $collectionName, $id, $data) {
        $collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
        //$data = $this->encrypt($data);
        $updateResult = $collection->updateOne(
            ['_id' => MongoDB::formatId($id)],
            ['$set' => $data]
        );
		return $this->find($database, $collectionName, $id);
    }

    public function delete($database, $collectionName, $id) {
		$collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
        $result = $collection->deleteOne(['_id' => MongoDB::formatId($id)]);
		return $result->getDeletedCount();
    }

    public function deleteWhere($database, $collectionName, $filter) {
		$collection = $this->client->selectDatabase($database)->selectCollection($collectionName);
        $result = $collection->deleteMany($filter);
		return $result->getDeletedCount();
    }
}

abstract class MongoDBAbstract {
    protected $client = null;

    protected function __construct($uri) {
        $this->client = new \MongoDB\Client($uri . "?retryWrites=true&w=majority");
    }

    public static function connect($uri = null) {
        global $mongodb;
        if (! isset($mongodb)) {
            $mongodb = [];
        }
        if (! $uri) {
            $uri = MONGO_URI;
        }
        $slug = slugify($uri);
        if (! isset($mongodb[$slug])) {
            $mongodb[$slug] = new MongoDB($uri);
        }
        return $mongodb[$slug];
    }
    
    protected static function parseArray($doc) {
        return json_decode(json_encode($doc), true);
    }
    // helper
	protected static function formatId($id) {
		if ($id instanceof \MongoDB\BSON\ObjectID) {
            return $id;
        }
		try {
            return new \MongoDB\BSON\ObjectID($id);
        } catch (\Exception $e) {
            return $id;
        }
	}
}