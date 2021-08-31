<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

class Controller
{

    /**
     * 返回当前实例
     * @return Controller
     */
    public static function instance()
    {
        return new static;
    }

    /**
     * @param string $name 控制器名称
     * @param $vae
     * @return mixed object || null
     * @throws \Exception
     */
    public function controller($name, $vae = '')
    {
        $suffix = strchr($name, '/');
        if (empty($suffix)) {
            $filePath = APP_PATH . MODE . '/controller/' . $name . '.php';
            $space = '\\app\\' . MODE . '\\controller\\' . $name;
        } else {
            $arr = explode('/', $name);
            $filePath = APP_PATH . $arr[0] . '/controller/' . $arr[1] . '.php';
            $space = '\\app\\' . $arr[0] . '\\controller\\' . $arr[1];
        }
        if (is_file($filePath)) {
            require_once $filePath;
            if (empty($vae)) :
                $object = new $space();
                return $object;
            endif;
        } else {
            halt('找不到:' . $filePath . ' 控制器');
        }
    }

    /**
     * @param string $name 控制器名称
     * @param string $action 方法名
     * @param array  $data 参数
     * @return mixed
     * @throws \Exception
     */
    public function action($name, $action, $data = [])
    {
        $this->controller($name, 1);
        $suffix = strchr($name, '/');

        if (!empty($suffix)) {
            $arr = explode('/', $name);
            $class = '\\app\\' . $arr[0] . '\\controller\\' . $arr[1];
            $object = new $class();
        } else {
            $class_name = '\\app\\' . MODE . '\\controller\\' . $name;
            $object = new $class_name();
        }

        if (method_exists($object, $action)) {
            return call_user_func_array([$object, $action], $data);
        } else {
            halt('不存在:' . $action . ' 方法');
        }
    }


    /**
     * 使用控制器
     * @param $str
     * @param $action
     * @return mixed
     * @throws \Exception
     */
    public function model($str = '', $action = '')
    {
        if (!empty($str)) {
            $suffix = strchr($str, '/');
        } else {
            $str = CONTROLLER;
        }

        if (isset($suffix) and !empty($suffix)) {
            $arr = explode('/', trim($str, '/'));
            $class = '\\app\\' . $arr[0] . '\\model\\' . $arr[1];
            $path = APP_PATH . $arr[0] . '/model/' . $arr[1] . '.php';
        } else {
            $class = '\\app\\' . MODE . '\\model\\' . $str;
            $path = APP_PATH . MODE . '/model/' . $str . '.php';
        }
        if (is_file($path)) {
            //
            $object = new $class();
            if (!is_null($action)) {
                //
                if (method_exists($object, $action)) {
                    return $object->$action();
                } else {
                    halt('模型不存在:' . $action . ' 方法');
                }
                //
            } else {
                return $object;
            }
            //
        } else {
            halt('找不到:' . $path . ' 模型');
        }
    }

    /**
     * 调用模板显示函数
     * @param $filename
     * @param null $data
     * @throws \Exception
     */
    public function view($filename, $data = null)
    {
        \ay\lib\View::view($filename, $data);
    }
}
