<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\drive;

class Route
{

    public static $get;
    public $mode;
    public $controller;
    public $action;
    private $field = '';

    public function __construct()
    {
        $this->router();
        $this->pathInfo();
    }

    //路由模式
    public function router()
    {

        $path = CONFIG . '/route.php';
        $path = str_replace('//', '/', $path);

        if (is_file($path)) {
            $rules = require $path;
        } else {
            $rules = [];
        }
//        p($rules);

        if (isset($_SERVER['REQUEST_URI']) and !empty($rules)) {
            $pathInfo = ltrim($_SERVER['REQUEST_URI'], "/");
            foreach ($rules as $k => $v) {
                $reg = "/" . $k . "/i";
                if (preg_match($reg, $pathInfo)) {
                    $res = preg_replace($reg, $v, $pathInfo);
                    $_SERVER['REQUEST_URI'] = '/' . $res;
                }
            }
        }

    }

    // pathInfo处理
    public function pathInfo()
    {

        if (strpos($_SERVER['REQUEST_URI'], '?')) {
            $path = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '?'));
        } else {
            $path = $_SERVER['REQUEST_URI'];
        }

        if ($path != '/') {

            $path = str_replace('//', '/', $path);
            $pathArr = explode('/', trim($path, '/'));

            // 允许public文件访问
            if ($pathArr[0] == 'public') {
                file_get_contents(ROOT . $path);
                exit;
            }

            // 获取mode 三种模式 绑定模块 版本分类 默认模式
            switch (true) {
                case (defined('BIND')):
                    define('CIND', str_replace('.php', '', $pathArr[0]));
                    $pathArr = $this->bq($pathArr);
                    $this->mode = BIND;
                    unset($pathArr[0]);
                    break;
                case (defined('VIND')):
                    if (!isset($pathArr[1])) $pathArr[1] = C('mode');
                    $this->mode = str_replace('.php', '/' . $pathArr[1], $pathArr[0]);
                    unset($pathArr[0]);
                    unset($pathArr[1]);
                    break;
                default:
                    $pathArr = $this->bq($pathArr);
                    if (strstr($pathArr[0], '.php')) {
                        $this->mode = str_replace('.php', '', $pathArr[1]);
                        unset($pathArr[0]);
                        unset($pathArr[1]);
                    } else {
                        $this->mode = str_replace('.php', '', $pathArr[0]);
                        unset($pathArr[0]);
                    }

            };
//            echo $this->mode;exit;
            $pathArr = array_merge($pathArr);

            // 获取controller
            if (!isset($pathArr[0])) $pathArr[0] = C('controller');
            $this->controller = $pathArr[0];
            unset($pathArr[0]);


            // 获取action
            if (!isset($pathArr[1])) $pathArr[1] = C('action');
            $this->action = str_replace('.' . C('REWRITE'), '', $pathArr[1]);
            unset($pathArr[1]);

            // 获取get
            if (!empty($pathArr)) {
                $sum = 1;
                foreach ($pathArr as $item):
                    if ($sum % 2 != 0) {
                        $this->field = $item;
                    } else {
                        self::$get[$this->field] = $item;
                        $this->field = '';
                    }
                    $sum++;
                endforeach;
            } else {
                self::$get = false;
            }

            //
        } else {
            $this->mode = C('mode');
            $this->controller = C('controller');
            $this->action = C('action');
            self::$get = false;
        }
    }

    private function bq($pathArr, $one = 1, $two = 2)
    {
        // 补全
        $num = count($pathArr);
        if ($num == $one) {
            $pathArr[] = C('controller');
            $pathArr[] = C('action');
        } else if ($num == $two) {
            $pathArr[] = C('action') . '.' . C('REWRITE');
        }
        return $pathArr;
    }

}
