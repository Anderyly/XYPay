<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

use ay\drive\Route;

class Request
{

    /**
     * 获取get参数
     * @param string $var   参数名
     * @param null $type    类型
     * @return array|bool|float|int|string|string[]|null
     */
    public static function get($var = '', $type = null)
    {
        $get = Route::$get;

        if ($get == false) {
            $get = $GLOBALS['_GET'];
            unset($get['/' . MODE . '/' . CONTROLLER . '/' . ACTION . '/']);
            unset($get['/' . MODE . '/' . CONTROLLER . '/' . ACTION]);
        }
        if (empty($var)) {
            return self::filter($get);
        } else {
            if (!self::has($var, 'get')) {
                return false;
            }

            $value = self::filter($get[$var]);
            return self::typeTo($value, $type);
        }

    }

    /**
     * 类型转换
     * @param bool|float|int|string|array $value    参数
     * @param string $type     类型
     * @return bool|float|int|string
     */
    private static function typeTo($value, $type)
    {
        if (gettype($value) != 'array') {
            switch ($type) {
                case 'string':
                    $value = (string) ($value);
                    break;
                case 'int':
                    $value = (int) ($value);
                    break;
                case 'float':
                    $value = (float) ($value);
                    break;
                case 'double':
                    $value = (double) ($value);
                    break;
                case 'bool':
                    $value = (bool) ($value);
                    break;
                default:
            }
            return $value;
        } else {
            return $value;
        }
    }

    /**
     *
     * @return bool|string
     */
    public static function url()
    {
        $str = self::filter($_SERVER['PATH_INFO']);
        return substr($str, 0, strrpos($str, '.'));
    }

    /**
     * 获取post参数
     * @param string $var   参数名
     * @param null $type    类型
     * @return array|bool|float|int|string|string[]|null
     */
    public static function post($var = '', $type = null)
    {

        $post = $GLOBALS['_POST'];
        if (empty($var)) {
            return self::filter($post);
        } else {
            if (!self::has($var, 'post')) {
                return false;
            }

            $value = self::filter($post[$var]);
            return self::typeTo($value, $type);
        }

    }

    /**
     * 获取全部参数
     * @return array
     */
    public static function param()
    {
        return array_merge(self::get(), self::post());
    }

    /**
     * 获取文件
     * @param null $var     参数名
     * @return array|string|string[]|null
     */
    public static function file($var = null)
    {
        if (empty($var)) {
            return $_FILES;
        } else {
            return $_FILES[$var];
        }
    }

    /**
     * 判断参数是否存在
     * @param string $var       参数名
     * @param string $type      传递方式
     * @return bool
     */
    public static function has($var, $type = 'ALL')
    {

        switch ($type) {
            case 'post':
                $return = array_key_exists($var, self::post());
                break;
            case 'get':
                $return = array_key_exists($var, self::get());
                break;
            default:
                if (array_key_exists($var, self::param())) {
                    $return = true;
                } else {
                    $return = true;
                }
        }

        return $return;

    }

    /**
     * 过滤参数
     * @param bool|float|int|string $str  值
     * @return array|string|string[]|null
     */
    private static function filter($str)
    {

        $farr = array(
            "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU", // xss
            "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU", // 特殊字符
            "/select\b|insert\b|update\b|delete\b|drop\b|create\b|like\b|and\b|or\b|values\b|set\b|exec\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is", // sql
        );
        if (is_array($str)) {
            //
            $arr = [];
            foreach ($str as $k => $v):
                if (is_array($v)) {
                    foreach ($v as $k1 => $v1):
                        $v1 = strip_tags(preg_replace($farr, '', $v1));
//                         if (empty($v1)) halt('参数含有非法字符，已被系统拦截');
                        $arr[$k][] = $v1;
                    endforeach;
                } else {
                    $v = strip_tags(preg_replace($farr, '', $v));
//                     if (empty($v)) halt('参数含有非法字符，已被系统拦截');
                    $arr[$k] = $v;
                }
            endforeach;
            return $arr;
            //
        } else {
            $str = preg_replace($farr, '', $str);
            $str = strip_tags($str);
            return $str;
        }

    }

}
