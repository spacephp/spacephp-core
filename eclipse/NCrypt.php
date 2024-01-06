<?php
namespace Eclipse;

class NCrypt {
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
        $encrypted = $data;//substr($data, $iv_length);
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }
}