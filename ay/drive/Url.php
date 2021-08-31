<?php

namespace ay\drive;

class Url {

    public static function instance()
    {
        return new self();
    }

    public function get($str, $data = null)
    {
        $arr = explode('/', trim($str, '/'));
        $len = count($arr);

        if ($len == 3) {
            $mode = $arr[0];
        } else {
            $mode = MODE;
        }
        if (defined('BIND')) {
            switch (true) {
                case ($len == 3) :
                    $mode = $arr[0];
                    break;
                case (BIND == MODE) :
                    $mode = CIND . '.php';
                    break;
                default :
                    $mode = MODE;
            }
        }

        switch ($len) {
            case 3 :
                $path = URL . '/' . $mode . '/' . $arr[1] . '/' . $arr[2];
                break;
            case 2 :
                $path = URL . '/' . $mode . '/' . $arr[0] . '/' . $arr[1];
                break;
            default :
                $path = URL . '/' . $mode . '/' . CONTROLLER . '/' . $arr[0];
        }

        $path .= '.' . C('REWRITE');

        if (is_array($data)) {
            $path .= '?';
            foreach ($data as $k => $v) {
                $path .= $k . '=' . $v . '&';
            }
            $path = rtrim($path, '&');
        } else {
            $path .= $data;
        }

        return $path;

    }
}