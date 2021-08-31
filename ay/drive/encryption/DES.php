<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn
 * @copyright Copyright (c) 2019
 */

namespace ay\drive\encryption;

class DES
{

    protected $method;
    protected $key;
    protected $output;
    protected $iv;
    protected $options;

    // output 的类型
    const OUTPUT_NULL = '';
    const OUTPUT_BASE64 = 'base64';
    const OUTPUT_HEX = 'hex';


    /**
     * DES constructor.
     * @param $key
     * @param string $method
     * @param string $output
     * @param string $iv
     * @param int $options
     */
    public function __construct($key = 'anderyly', $method = 'DES-CBC', $output = 'base64', $iv = '66668888', $options = OPENSSL_RAW_DATA | OPENSSL_NO_PADDING)
    {
        if (strlen($key) != 8) return 'key Length greater than 8';
        if (strlen($iv) != 8) return 'iv Length greater than 8';

        $this->key = $key;
        $this->method = $method;
        $this->output = $output;
        $this->iv = $iv;
        $this->options = $options;
    }
    
    public static function instance()
    {
        return new self;
    }


    /**
     * 加密
     * @param $str
     * @return string
     */
    public function encode($str)
    {
        $str = $this->pkcsPadding($str, 8);
        $sign = openssl_encrypt($str, $this->method, $this->key, $this->options, $this->iv);
        if ($this->output == self::OUTPUT_BASE64) {
            $sign = base64_encode($sign);
        } else if ($this->output == self::OUTPUT_HEX) {
            $sign = bin2hex($sign);
        }
        return $sign;
    }


    /**
     * 解密
     * @param $encrypted
     * @return string
     */
    public function decode($encrypted)
    {
        if ($this->output == self::OUTPUT_BASE64) {
            $encrypted = base64_decode($encrypted);
        } else if ($this->output == self::OUTPUT_HEX) {
            $encrypted = hex2bin($encrypted);
        }
        $sign = openssl_decrypt($encrypted, $this->method, $this->key, $this->options, $this->iv);
        $sign = $this->unPkcsPadding($sign);
        $sign = rtrim($sign);
        return $sign;
    }


    /**
     * 填充
     * @param $str
     * @param $blocksize
     * @return string
     */
    private function pkcsPadding($str, $blockSize)
    {
        $pad = $blockSize - (strlen($str) % $blockSize);
        return $str . str_repeat(chr($pad), $pad);
    }


    /**
     * 去填充
     * @param $str
     * @return bool|string
     */
    private function unPkcsPadding($str)
    {
        $pad = ord($str{strlen($str) - 1});
        if ($pad > strlen($str)) {
            return false;
        }
        return substr($str, 0, -1 * $pad);
    }
}