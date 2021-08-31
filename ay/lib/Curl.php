<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

class Curl
{
    private $ch = null;
    private static $url;
    private $params = null;

    public function __construct()
    {
        $this->ch = curl_init();
    }

    /**
     * 设置url地址
     * @param $url
     * @return Curl|bool
     */
    public static function url($url)
    {
        if (empty($url)) {
            return false;
        }

        self::$url = $url;
        return new self();
    }

    /**
     * 设置http header
     * @param $header
     * @return $this
     */
    public function header($header)
    {
        if (is_array($header)) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        }

        return $this;
    }

    /**
     * 设置http 超时
     * @param int $time
     * @return $this
     */
    public function time($time)
    {
        if ($time <= 0) {
            $time = 5;
        }

        curl_setopt($this->ch, CURLOPT_TIMEOUT, $time);
        return $this;
    }

    /**
     * 设置http 代理
     * @param string $proxy
     * @param int $proxy
     * @return $this
     */
    public function proxy($proxy, $port)
    {
        if ($proxy) {
            curl_setopt($this->ch, CURLOPT_PROXY, $proxy);
        }

        if (is_int($port)) {
            curl_setopt($this->ch, CURLOPT_PROXYPORT, $port);
        }

        return $this;
    }

    /**
     * 设置来源页面
     * @param string $referer
     * @return $this
     */
    public function referer($referer = "")
    {
        if (!empty($referer)) {
            curl_setopt($this->ch, CURLOPT_REFERER, $referer);
        }

        return $this;
    }

    /**
     * 模拟用户使用的浏览器
     * @param string $agent
     * @return $this
     */
    public function userAgent($agent = "")
    {
        if ($agent) {
            curl_setopt($this->ch, CURLOPT_USERAGENT, $agent);
        }

        return $this;
    }

    /**
     * http响应中是否显示header，1表示显示
     * @param $show
     * @return $this
     */
    public function show($show)
    {
        curl_setopt($this->ch, CURLOPT_HEADER, $show);
        return $this;
    }

    /**
     * 设置http请求的参数,get或post
     * @param array $params
     * @return $this
     */
    public function param($params)
    {
        $this->httpParams = $params;
        return $this;
    }

    /**
     * 设置证书路径
     * @param $file
     */
    public function cert($file)
    {
        curl_setopt($this->ch, CURLOPT_CAINFO, $file);
    }

    /**
     * 模拟GET请求
     * @param string $dataType
     * @return bool|mixed
     */
    public function get($dataType = 'text')
    {
        if (stripos(self::$url, 'https://') !== false) {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->ch, CURLOPT_SSLVERSION, 1);
        }
        // 设置get参数
        if (!empty($this->httpParams) && is_array($this->httpParams)) {
            if (strpos(self::$url, '?') !== false) {
                self::$url .= http_build_query($this->httpParams);
            } else {
                self::$url .= '?' . http_build_query($this->httpParams);
            }
        }
        // end 设置get参数
        curl_setopt($this->ch, CURLOPT_URL, self::$url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($this->ch);
        $status = curl_getinfo($this->ch);
        curl_close($this->ch);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            if ($dataType == 'json') {
                $content = json_decode($content, true);
            }
            return $content;
        } else {
            return false;
        }
    }

    /**
     * 模拟POST请求
     * @param string $dataType
     * @return mixed
     */
    public function post($dataType = 'text')
    {
        if (stripos(self::$url, 'https://') !== false) {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->ch, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($this->ch, CURLOPT_URL, self::$url);
        // 设置post body
        if (!empty($this->httpParams)) {
            if (is_array($this->httpParams)) {
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($this->httpParams));
            } elseif (is_string($this->httpParams)) {
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->httpParams);
            }
        }
        // end 设置post body
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POST, true);
        $content = curl_exec($this->ch);
        $status = curl_getinfo($this->ch);
        curl_close($this->ch);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            if ($dataType == 'json') {
                $content = json_decode($content, true);
            }
            return $content;
        } else {
            return false;
        }
    }
}
