<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\drive;

class Hook
{
    //钓子
    private static $hook = array();

    /**
     * 添加钓子事件
     * @param string $hook 钓子名称
     * @param string $action 钓子事件
     */
    public static function add($hook, $action)
    {

        if (!isset(self::$hook[$hook])) {
            self::$hook[$hook] = array();
        }
        if (is_array($action)) {
            self::$hook[$hook] = array_merge(self::$hook[$hook], $action);
        } else {
            self::$hook[$hook][] = $action;
        }
    }

    /**
     * 获得钓子信息
     * @param string $hook 钓子名
     * @return array
     */
    public static function get($hook = '')
    {
        if (empty($hook)) {
            return self::$hook;
        } else {
            return self::$hook[$hook];
        }
    }

    /**
     * 批量导入钓子
     * @param array $data 钓子数据
     * @param bool $recursive 是否递归合并
     */
    public static function import($data, $recursive = true)
    {
        if ($recursive === false) {
            self::$hook = array_merge(self::$hook, $data);
        } else {
            foreach ($data as $hook => $value) {
                if (!isset(self::$hook[$hook])) {
                    self::$hook[$hook] = array();
                }
                if (isset($value['_overflow'])) {
                    unset($value['_overflow']);
                    self::$hook[$hook] = $value;
                } else {
                    self::$hook[$hook] = array_merge(self::$hook[$hook], $value);
                }
            }
        }
    }

    /**
     * 监听钓子
     * @param string $hook 钓子名
     * @param null $param 参数
     * @return bool|void
     */
    public static function listen($hook, &$param = null)
    {
        if (!isset(self::$hook[$hook])) {
            return false;
        }
        foreach (self::$hook[$hook] as $name) {
            if (false == self::exe($name, $hook, $param)) {
                return;
            }
        }
    }

    /**
     * 执行钓子
     * @param string $name 钓子名
     * @param string $action 钓子方法
     * @param null $param 参数
     * @return boolean
     */
    public static function exe($name, $action, &$param = null)
    {
        if (substr($name, -4) == 'Hook') { //钓子
            $action = 'run';
        } else { //插件
            require_cache(APP_ADDON_PATH . $name . '/' . $name . 'Addon.php');
            $name = $name . 'Addon';
            if (!class_exists($name, false)) {
                return false;
            }
        }
        $obj = new $name;
        if (method_exists($obj, $action)) {
            $obj->$action($param);
        }
        return true;
    }
}
