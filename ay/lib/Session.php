<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

class Session
{
    public static function set($name, $var = '', $path = '')
    {
        $_SESSION[$name] = $var;
        return true;
    }

    public static function get($name, $var = '', $path = '')
    {

        if (!isset($_SESSION[$name])) {
            return false;
        } elseif ($_SESSION[$name] == $var and !empty($var)) {
            if (empty($path) and empty($var)) {
                return $_SESSION[$name];
            }
        } else {
            if (empty($path) and empty($var)) {
                return $_SESSION[$name];
            }
        }

    }

    public static function has($name, $var = '', $path = '')
    {
        // p($_SESSION['user']);exit;
        if (!isset($_SESSION[$name])) {
            return false;
        } elseif ($_SESSION[$name] == $var and !empty($var)) {
            return true;
        } else {
            return true;
        }
    }

    public static function delete($name = '', $path = '')
    {
        if (empty($name)) {
            $_SESSION = [];
        } else if (empty($path) and !empty($name)) {
            unset($_SESSION[$name]);
        }
    }

    public static function pull($name = '', $path = '')
    {
        if (empty($name)) {
            $_SESSION = [];
        } elseif (empty($path) and !empty($name)) {
            $data = self::get($name);
            unset($_SESSION[$name]);
        }

        return $data;
    }

}
