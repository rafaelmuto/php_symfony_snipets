<?php

namespace App\Helper;

class ApiKeyEncryptionHelper
{
    public static function encrypt(string $data, string $passphrase, string $cipher_algo = 'AES-256-CBC'): ?string
    {
        $ivLen = openssl_cipher_iv_length($cipher_algo);

        $iv = openssl_random_pseudo_bytes($ivLen);

        $api_key = openssl_encrypt($data, $cipher_algo, $passphrase, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $api_key);
    }

    public static function decrypt(string $data, string $passphrase, string $cipher_algo = 'AES-256-CBC'): ?string
    {
        $encrypted_data = base64_decode($data);

        $ivLen = openssl_cipher_iv_length($cipher_algo);

        $iv = substr($encrypted_data, 0, $ivLen);

        $encrypted_api_key = substr($encrypted_data, $ivLen);

        return openssl_decrypt($encrypted_api_key, $cipher_algo, $passphrase, OPENSSL_RAW_DATA, $iv);
    }
}
