<?php
namespace Particle\Models;

use Particle\Database\Migration;

class Document extends Model {
	public static $table = 'documents';
	public static $fillable = ['_id', 'host', 'collection', 'data', 'created_at', 'updated_at'];

	public static function create($data) {
		if (! isset($data['host'])) {
			$data['host'] = host_name();
		}
		if (! isset($data['_id'])) {
			$data['_id'] = Document::generateUUID();
		}
		if (! isset($data['collection'])) {
			$class = get_called_class();
			$data['collection'] = $class::$collection;
		}
		if (! isset($data['data'])) {
			$data['data'] = '{}';
		}
		return parent::create($data);
	}

	private static function generateUUID() {
		return str_replace('.', '', uniqid('', true));
	}

	public static function migrate($table, $force = false) {
		return Migration::run($table, [
            '_id VARCHAR(64)',
            'host VARCHAR(50)',
            'collection VARCHAR(50) NOT NULL',
            'data JSON',
            'created_at timestamp DEFAULT CURRENT_TIMESTAMP',
            'updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'UNIQUE (_id, host)',
            'INDEX (collection, created_at, updated_at)'
        ], $force);
	}
}