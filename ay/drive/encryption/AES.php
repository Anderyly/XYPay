<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn
 * @copyright Copyright (c) 2019
 */

namespace ay\drive\encryption;

class AES
{

    protected $method;
    protected $secret_key;
    protected $iv;
    protected $options;


    public function __construct($key = 'anderyly', $method = 'AES-128-ECB', $iv = '', $options = 0)
    {
        $this->secret_key = $key;
        $this->method = $method;
        $this->iv = $iv;
        $this->options = $options;
    }
    
    public static function instance()
    {
        return new self();
    }


    public function encode($data)
    {
        return openssl_encrypt($data, $this->method, $this->secret_key, $this->options, $this->iv);
    }

    public function decode($data)
    {
        return openssl_decrypt($data, $this->method, $this->secret_key, $this->options, $this->iv);
    }
}