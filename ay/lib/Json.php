<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

class Json
{
    /**
     * 返回json数据
     * @param int $code 状态码
     * @param string $msg 返回的信息
     * @param array $data info里面的数组
     * @param array $zsy 一级字段
     * @return string Json字符串
     */
    public static function msg($code, $msg = "", $data = [], $zsy = null)
    {
        // 智能判断 列表必须包含list 或者 all
        $num = count($data);
        if (strstr(strtolower(ACTION), 'list') or strstr(strtolower(ACTION), 'all')) {
            if ($num != count($data, 1)) {
                $data = ['info' => $data];
            } else {
                $data = ['info' => []];
            }
        } else {
            if ($num == 0) {
                $data = new \stdClass;
            }

        }

        $d = [
            "code" => $code,
            "msg" => $msg,
            "data" => $data,
        ];

        // 同级
        if (!is_null($zsy)):
            foreach ($zsy as $key => $value) {
                $d[$key] = $value;
            }
        endif;

        header('Content-Type: application/json; charset=utf-8');
        exit(json_encode($d, JSON_UNESCAPED_UNICODE));
    }
}
