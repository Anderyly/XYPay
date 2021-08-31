<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

class Redis
{

    protected array $options = [];
    protected \Redis $handler;

    /**
     * 构造函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($conf = '')
    {
        if (is_array($conf)) {
            $options = $conf;
        } else {
            $options = C();
        }
        if (!extension_loaded('redis')) {
            exit('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = new \Redis;
        if ($this->options['REDIS_PERSISTENT']) {
            $this->handler->pconnect($this->options['REDIS_HOST'], $this->options['REDIS_PORT'], $this->options['REDIS_TIMEOUT'], 'persistent_id_' . $this->options['REDIS_SELECT']);
        } else {
            $this->handler->connect($this->options['REDIS_HOST'], $this->options['REDIS_PORT'], $this->options['REDIS_TIMEOUT']);
        }

        if ('' != $this->options['REDIS_PASS']) {
            $this->handler->auth($this->options['REDIS_PASS']);
        }

        if (0 != $this->options['REDIS_SELECT']) {
            $this->handler->select($this->options['REDIS_SELECT']);
        }
    }

    public function lpush($list_key, $lisat_data)
    {
        return $this->handler->lpush($list_key, $lisat_data);
    }

    public function rpush($list_key, $lisat_data)
    {
        return $this->handler->rpush($list_key, $lisat_data);
    }

    public function lpop($list_key)
    {
        return $this->handler->lpop($list_key);
    }

    public function rpop($list_key)
    {
        return $this->handler->rpop($list_key);
    }

    /**
     * 判断缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        return $this->handler->exists($this->getCacheKey($name));
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $value = $this->handler->get($this->getCacheKey($name));
        if (is_null($value) || false === $value) {
            return $default;
        }

        try {
            $result = 0 === strpos($value, 'think_serialize:') ? unserialize(substr($value, 16)) : $value;
        } catch (\Exception $e) {
            $result = $default;
        }

        return $result;
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param integer|\DateTime $expire 有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
        if ($this->tag && !$this->has($name)) {
            $first = true;
        }
        $key = $this->getCacheKey($name);
        $value = is_scalar($value) ? $value : 'think_serialize:' . serialize($value);
        if ($expire) {
            $result = $this->handler->setex($key, $expire, $value);
        } else {
            $result = $this->handler->set($key, $value);
        }
        isset($first) && $this->setTagItem($key);
        return $result;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        $key = $this->getCacheKey($name);

        return $this->handler->incrby($key, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        $key = $this->getCacheKey($name);

        return $this->handler->decrby($key, $step);
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        return $this->handler->del($this->getCacheKey($name));
    }

    /**
     * 清除缓存
     * @access public
     * @param string $tag 标签名
     * @return boolean
     */
    public function clear($tag = null)
    {
        if ($tag) {
            // 指定标签清除
            $keys = $this->getTagItem($tag);
            foreach ($keys as $key) {
                $this->handler->del($key);
            }
            $this->rm('tag_' . md5($tag));
            return true;
        }
        return $this->handler->flushDB();
    }

    protected function getTagItem($tag)
    {
        $key = 'tag_' . md5($tag);
        $value = $this->get($key);
        if ($value) {
            return array_filter(explode(',', $value));
        } else {
            return [];
        }
    }

    protected function getCacheKey($name)
    {
        return $this->options['REDIS_PREFIX'] . $name;
    }

}
