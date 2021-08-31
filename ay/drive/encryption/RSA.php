<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn
 * @copyright Copyright (c) 2019
 */

class RSA
{

    /**
     * 私钥加密
     * @param $private_key
     * @param $data
     * @return bool|string
     */
    public function privateEncode($private_key, $data)
    {
        $encrypted = '';
        $pi_key = openssl_pkey_get_private($private_key);
        $plainData = str_split($data, 100);
        foreach ($plainData as $chunk) {
            $partialEncrypted = '';
            $encryptionOk = openssl_private_encrypt($chunk, $partialEncrypted, $pi_key);
            if ($encryptionOk === false) {
                return false;
            }
            $encrypted .= $partialEncrypted;
        }

        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    /**
     * 公钥解密
     * @param $public_key
     * @param $data
     * @return bool|string
     */
    public function publicDecode($public_key, $data)
    {
        $decrypted = '';
        $pu_key = openssl_pkey_get_public($public_key);
        $plainData = str_split(base64_decode($data), 128);
        foreach ($plainData as $chunk) {
            $str = '';
            $decryptionOk = openssl_public_decrypt($chunk, $str, $pu_key);
            if ($decryptionOk === false) {
                return false;
            }
            $decrypted .= $str;
        }
        return $decrypted;
    }


    /**
     * 公钥加密
     * @param $public_key
     * @param $data
     * @return bool|string
     */
    public function publicEncode($public_key, $data)
    {
        $encrypted = '';
        $pu_key = openssl_pkey_get_public($public_key);
        $plainData = str_split($data, 100);
        foreach ($plainData as $chunk) {
            $partialEncrypted = '';
            $encryptionOk = openssl_public_encrypt($chunk, $partialEncrypted, $pu_key);
            if ($encryptionOk === false) {
                return false;
            }
            $encrypted .= $partialEncrypted;
        }
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    /**
     * 私钥解密
     * @param $private_key
     * @param $data
     * @return bool|string
     */
    public function privateDecode($private_key, $data)
    {
        $decrypted = '';
        $pi_key = openssl_pkey_get_private($private_key);
        $plainData = str_split(base64_decode($data), 128);
        foreach ($plainData as $chunk) {
            $str = '';
            $decryptionOk = openssl_private_decrypt($chunk, $str, $pi_key);
            if ($decryptionOk === false) {
                return false;
            }
            $decrypted .= $str;
        }
        return $decrypted;
    }

}
