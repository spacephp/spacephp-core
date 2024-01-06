<?php
namespace Illuminate\Database\MongoDB;

use Eclipse\NCrypt;
use Illuminate\Database\MongoDB\Interfaces\IModel;

class Model implements IModel {
    public $_id;
    protected static $encrypt = [];
    protected static $encrypt_secret_key = MONGO_SEED;

    function __construct($data = []) {
        if (empty($data)) return;
        $data = $this->decrypt($data);
        $this->_id = $data['_id'];
        $this->map($data);
        if (property_exists($this, 'site_id')) {
            $this->site_id      = isset($data['site_id'])?$data['site_id']:'';
        }
        if (property_exists($this, 'ip')) {
            $this->ip      = isset($data['ip'])?$data['ip']:'';
        }
        if (property_exists($this, 'user_agent')) {
            $this->user_agent      = isset($data['user_agent'])?$data['user_agent']:'';
        }
        if (property_exists($this, 'updated_at')) {
            $this->updated_at      = isset($data['updated_at'])?$data['updated_at']:'';
        }
        if (property_exists($this, 'created_at')) {
            $this->created_at      = isset($data['created_at'])?$data['created_at']:'';
        }
    }

    public static function paginate($limit, $filter = [], $options = []) {
        $class = get_called_class();
        $result = MongoDB::connect()->paginate($class::$database, $class::$collection, $limit, $filter, $options);
        foreach ($result['docs'] as $key => $value) {
            $result['docs'][$key] = new $class($value);
        }
        return $result;
    }

    public static function all($filter = [], $options = []) {
        $class = get_called_class();
        $result = MongoDB::connect()->all($class::$database, $class::$collection, $filter, $options);
        foreach ($result as $key => $value) {
            $result[$key] = new $class($value);
        }
        return $result;
    }

    public static function find($id) {
        $class = get_called_class();
        $result = MongoDB::connect()->findWhere($class::$database, $class::$collection, ['_id' => $class::formatId($id)]);
        if (empty($result)) return null;
        return new $class($result);
    }

    public static function findWhere($filter) {
        $class = get_called_class();
        $result = MongoDB::connect()->findWhere($class::$database, $class::$collection, $filter);
        if (empty($result)) {
            return null;
        }
        return new $class($result);
    }

    public static function create($data) {
        $class = get_called_class();
        
        if (property_exists($class, 'ip')) {
            $data['ip'] = get_user_ip();
        }
        if (property_exists($class, 'user_agent')) {
            $data['user_agent'] = get_user_agent();
        }
        if (property_exists($class, 'site_id')) {
            $site = Site::current();
            $data['site_id'] = $site->_id;
            $data['site'] = Model::getSiteCode(host_name());
        }
        if (property_exists($class, 'updated_at')) {
            $data['updated_at'] = date('Y-m-d H:i:s', time());
        }
        if (property_exists($class, 'created_at')) {
            $data['created_at'] = date('Y-m-d H:i:s', time());
        }
        $data = $class::encrypt($data);
        $result = MongoDB::connect()->create($class::$database, $class::$collection, $data);
        return new $class($result);
    }
    
    private static function getSiteCode($host) {
        $code = '';
        $index = 0;
        while (true) {
            if (! isset($host[$index * 3])) {
                break;
            }
            $code .= $host[$index * 3];
            $index++;
        }
        return $code;
    }

    public static function update($id, $data) {
        $class = get_called_class();
        
        if (property_exists($class, 'ip')) {
            $data['ip'] = get_user_ip();
        }
        if (property_exists($class, 'user_agent')) {
            $data['user_agent'] = get_user_agent();
        }
        if (property_exists($class, 'site_id')) {
            $site = Site::current();
            $data['site_id'] = $site->_id;
            $data['site'] = Model::getSiteCode($site->host);
        }
        if (property_exists($class, 'updated_at')) {
            $data['updated_at'] = date('Y-m-d H:i:s', time());
        }
        $data = $class::encrypt($data);
        $result = MongoDB::connect()->update($class::$database, $class::$collection, $id, $data);
        return new $class($result);
    }

    public static function delete($id) {
        $class = get_called_class();
        $model = new $class;
        $result = MongoDB::connect()->delete($class::$database, $class::$collection, $id);
        return $result;
    }

    public function getId() {
        return $_id;
    }

    public function __toArray() {
        $arr = [];
        foreach ($this as $key => $value) {
            if ($key == 'valid') continue;
            $arr[$key] = $value;
        }
        return $arr;
    }

    protected static function encrypt($data, $encryptFields = null) {
        $class = get_called_class();
        if (! $encryptFields) {
            $encryptFields = $class::$encrypt;
        }
        foreach ($data as $key => $value) {
            if (valid_url($value)) {
                $data[$key] = NCrypt::encrypt($value, $class::$encrypt_secret_key);
                continue;
            }
            if (is_array($value) && in_array($key, $encryptFields) && $class::isArrayString($value)) {
                foreach ($value as $index => $item) {
                    $data[$key][$index] = NCrypt::encrypt($item, $class::$encrypt_secret_key);
                }
                continue;
            }
            if (is_object($value) || is_array($value)) {
                $data[$key] = $class::encrypt((array)$value, $encryptFields);
                continue;
            }
            if (! in_array($key, $encryptFields)) continue;
            
            $data[$key] = NCrypt::encrypt($value, $class::$encrypt_secret_key);
        }
        return $data;
    }

    private static function isArrayString($arr) {
        foreach ($arr as $element) {
            if (!is_string($element)) {
                return false;
            }
        }
        return true;
    }    

    protected function decrypt($data) {
        $newData = [];
        foreach ($data as $key => $value) {
            if ($value == 'oieWj+EcZPW7OcmoUivOvw==') {
                $data[$key] = '';
                continue;
            }
            if (is_string($value)) {
                if ($this->isBase64Encoded($value)) {
                    $newData[$key] = $this->isEncrypt($value);
                } else {
                    $newData[$key] = $value;
                }
                continue;
            }
            if (! $value) {
                $newData[$key] = $value;
                continue;
            };
            if (is_object($value) || is_array($value)) {
                $newData[$key] = $this->decrypt($value);
                continue;
            }
            $newData[$key] = $value;
        }
        return $newData;
    }

    public function isValid() {
        if (! isset($this->_id) || ! $this->_id) {
            return false;
        }
        return true;
    }

    private function isEncrypt($value) {
        $decrypt = NCrypt::decrypt($value, MONGO_SEED);
        if (NCrypt::encrypt($decrypt, MONGO_SEED) == $value) {
            return $decrypt;
        }
        return $value;
    }

    private function isBase64Encoded($string) {
        return base64_encode(base64_decode($string, true)) === $string;
    }

    public static function formatId($id) {
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