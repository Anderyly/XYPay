<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2019
 */

namespace ay\lib;

use ay\lib\Json;

class Api
{

    public $data = [];
    public $rule = [];
    public $time = 6000;
    public $mode = 'api';
    public $key = "anderyly";

    public function __construct($mode = null, $rule = null, $key = null, $time = 6000, $status = 1, $format = 'param')
    {
        if (!is_null($key)) {
            $this->key = $key;
        }

        if (!is_null($time)) {
            $this->time = $time;
        }

        if (!is_null($rule)) {
            $this->rule = $rule;
        }

        if (!is_null($mode)) {
            $this->mode = $mode;
        }

        $this->data = R($format);
        if ($status == 1) {
            $this->checkTimeSign($this->data);
        }

    }

    private function checkTimeSign($data)
    {

        $other = strtolower('/' . $this->mode . '/' . CONTROLLER . '/' . ACTION);
        unset($data[$other]);
        if (isset($data[$other])) {
            $other = strtolower('/' . $this->mode . '/' . CONTROLLER . '/' . ACTION . '/');
            unset($data[$other]);
        }

        // 验证规则是否不用认证
        if (!in_array($other, $this->rule)):
            // 时间戳验证
            if (!isset($data['timestamp']) or intval($data['timestamp']) <= 1) {
                Json::msg(400, "timestamp不能为空或不存在");
            } else {
                // 时间戳对比
                if ($this->getTime() - intval($data['timestamp']) > $this->time):
                    Json::msg(400, "请求超时");
                endif;
            }

            if (!isset($data['sign'])) {
                Json::msg(400, 'sign不存在');
            }

            $sign = $data['sign'];
            unset($data['sign']);

            // 排序
            ksort($data);
            foreach ($data as $k => $v):
                if (!empty($v)) {
                    $arr[] = $k . '=' . $v;
                }

            endforeach;
            $str = implode("&", $arr);
            $str .= '&key=' . $this->key;
            if ($sign != MD5($str)) {
                Json::msg(400, "sign验证错误");
            }

        endif;

        $this->data = $data;
    }

    private function getTime()
    {
        $arr = explode('.', microtime(true));
        return (float) ($arr[0] . substr($arr[1], 0, 3));
    }
}
