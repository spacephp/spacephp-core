<?php
namespace Illuminate\Database;

class NCrypt {
    public static function encryptDoc($data, $secret_key) {
        foreach ($data as $key => $value) {
            if ($key == 'paypal_client_id') {
                $data[$key] = NCrypt::encrypt($value, $secret_key);
				continue;
			};
            if (in_array($key, ['_id', 'site_id', 'password', 'role', 'status', 'ip', 'user_agent', 'session_key'])) continue;
            if (strpos($key, '_id') !== false) continue;
            
            if (is_string($value) && strtotime($value) > 0) continue;
            if (is_array($value)) {
                $data[$key] = NCrypt::encryptDoc($value, $secret_key);
                continue;
            }
            if (is_numeric($value)) continue;
			$data[$key] = NCrypt::encrypt($value, $secret_key);
		}
		return $data;
    }

    public static function decryptDoc($data, $secret_key) {
        if (! is_array($data)) {
            $data = (array) $data;
        }
		foreach ($data as $key => $value) {
            if ($key == 'paypal_client_id') {
                $data[$key] = NCrypt::decrypt($value, $secret_key);
				continue;
			};
            if ($key == '_id') {
				$data['_id'] = strval($value);
				continue;
			}
            if (in_array($key, ['_id', 'site_id', 'password', 'role', 'status', 'ip', 'user_agent', 'session_key'])) continue;
			if (is_numeric($value)) continue;
			if (strpos($key, '_id') !== false) continue;
			if (is_string($value) && strtotime($value) > 0) continue;
			if (is_string($value)) {
                $data[$key] = NCrypt::decrypt($value, $secret_key);
				continue;
			};
			$data[$key] = NCrypt::decryptDoc((array) $value, $secret_key);
		}
		return $data;
	}

    public static function encrypt($data, $key)
    {
        if (! $data) {
            return '';
        }
        $iv = base64_decode('3afMtYYe5DjKgTwUG07O3Q==');
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $result = base64_encode($encrypted);
        
        return $result;
    }

    public static function decrypt($data, $key)
    {
        $data = base64_decode($data);
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $iv_length);
        $iv = base64_decode('3afMtYYe5DjKgTwUG07O3Q==');
        //$iv = '3afMtYYe5DjKgTwUG07O3';
        $encrypted = $data;//substr($data, $iv_length);
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }
}