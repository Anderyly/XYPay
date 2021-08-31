<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

use ay\lib\Json;

class Validator
{
    public static function check($data, $param)
    {
        if (!is_array($param)) {
            halt('param is not array');
        }

        // 获取用户自定义验证
        $path = APP_PATH . MODE . '/validator/' . CONTROLLER . '.php';
        if (file_exists($path)) {
            $userConfig = require_once $path;
        } else {
            $userConfig = [];
        }

        foreach ($param as $k => $v):
            if (empty($v)) {
                continue;
            }

            $arr = explode('|', $v);

            foreach ($arr as $v1):

                switch ($v1) {
                    case 'require':
                        if (!isset($data[$k])) {
                            $msg = self::checkUserConfig($userConfig, $k, $v1, '不存在');
                        }
                        break;
                    case 'empty':
                        if (empty($data[$k])) {
                            $msg = self::checkUserConfig($userConfig, $k, $v1, '不能为空');
                        }
                        break;
                    case (strstr($v1, 'max')):
                        $len = strlen($data[$k]);
                        $strLen = substr($v1, strripos($v1, ':') + 1);
                        if ($strLen < $len) {
                            $msg = self::checkUserConfig($userConfig, $k, $v1, '超过' . $strLen . '个字符');
                        }
                        break;
                    case (strstr($v1, 'min')):
                        $len = strlen($data[$k]);
                        $strLen = substr($v1, strripos($v1, ':') + 1);
                        if ($strLen > $len) {
                            $msg = self::checkUserConfig($userConfig, $k, $v1, '低于' . $strLen . '个字符');
                        }

                        break;
                    case (strstr($v1, 'len')):
                        $len = strlen($data[$k]);
                        $strLen = substr($v1, strripos($v1, ':') + 1);
                        if ($strLen != $len) {
                            $msg = self::checkUserConfig($userConfig, $k, $v1, '不等于' . $strLen . '个字符');
                        }

                        break;
                    case 'mobile' :
                        if (!self::mobile($data[$k])) {
                            $msg = self::checkUserConfig($userConfig, $k, $v1, '手机号错误');
                        }
                        break;
                    case 'card' :
                        if (!self::cardId($data[$k])) {
                            $msg = self::checkUserConfig($userConfig, $k, $v1, '身份证错误');
                        }
                        break;
                    case 'email' :
                        if (!self::email($data[$k])) {
                            $msg = self::checkUserConfig($userConfig, $k, $v1, '邮箱错误');
                        }
                        break;
                }
                if (isset($msg)) {
                    Json::msg(400, $msg);
                }

            endforeach;

        endforeach;
    }

    private static function checkUserConfig($userConfig, $k, $v1, $msg) {
        if (!isset($userConfig[ACTION][$k][$v1])) {
            return $k . $msg;
        } else {
            return $userConfig[ACTION][$k][$v1];
        }
    }

    /**
     * 移动手机号判断
     * @param $mobile
     * @return bool
     */
    public static function mobile($mobile)
    {
        $preg = "/^1[34578]\d{9}$/";
        if (preg_match($preg, $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证身份证号（15位或18位数字）
     * @param $cardId
     * @return bool
     */
    public static function cardId($cardId)
    {
        $preg = "/^\d{15}|\d{18}$/";
        if (preg_match($preg, $cardId)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证用户密码（以字母开头，长度在6-18之间，只能包含字符、数字和下划线）
     * @param $str
     * @return bool
     */
    public static function pass($str)
    {
        $preg = "/^[a-zA-Z]\w{5,17}$/";
        if (preg_match($preg, $str)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证邮箱地址
     * @param string email
     * @return bool
     */
    public static function email($email)
    {
        $preg = "/^\w+[-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/";
        if (preg_match($preg, $email)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证汉字
     * @param $characters
     * @return bool
     */
    public static function chinese($characters)
    {
        $preg = "/^[\u4e00-\u9fa5],{0,}$/";
        if (preg_match($preg, $characters)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证url地址
     * @param $str
     * @return bool
     */
    public static function link($str)
    {
        $preg = "/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&amp;\+\%]*/is";
        if (preg_match($preg, $str)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证qq号
     * @param $number
     * @return bool
     */
    public static function qq($number)
    {
        $preg = "/^[1-9][0-9]{4,}$/";
        if (preg_match($preg, $number)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证邮政编码
     * @param $number
     * @return bool
     */
    public static function post($number)
    {
        $preg = "/^[1-9]\d{5}(?!\d)$/";
        if (preg_match($preg, $number)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证ip地址
     * @param $ip
     * @return bool
     */
    public static function ip($ip)
    {
        $preg = "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/";
        if (preg_match($preg, $ip)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证是否是数字 不含小数
     * @param $number
     * @return bool
     */
    public static function digit($number)
    {
        $preg = "/^\d*$/";
        if (preg_match($preg, $number)) {
            return true;
        } else {
            return false;
        }
    }
}
