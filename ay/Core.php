<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay;

use ay\drive\Log;
use ay\drive\Route;
use Exception;

final class Core
{
    public static function run()
    {
        self::_init();
        self::route();
    }

    /**
     * 路由加载
     * @throws Exception
     */
    private static function route()
    {

        $route = new Route();
        $mode = $route->mode;
        $controller = str_replace('.', '/', ucfirst($route->controller));
        $action = $route->action;

        // 定义请求常量
        defined('MODE') or define('MODE', $mode);
        defined('CONTROLLER') or define('CONTROLLER', $controller);
        defined('ACTION') or define('ACTION', $action);

        Log::visit(MODE, CONTROLLER, ACTION);

        // 判断控制器是否存在
        if (is_file(APP_PATH . MODE . '/controller/' . CONTROLLER . '.php')) {
            // 实例化控制器
            $controllerClass = '\\app\\' . MODE . '\\controller\\' . CONTROLLER;
            $controllerClass = str_replace('/', '\\', $controllerClass);
            $controllerS = new $controllerClass();
            if (method_exists($controllerS, ACTION)) {

                // 加载模块下自定义函数
                if (file_exists(APP_PATH . MODE . "/function.php")) {
                    require_once APP_PATH . MODE . "/function.php";
                }
                call_user_func_array(array($controllerS, $action), []);
            } else {
                halt('不存在:' . $action . ' 方法');
            }
        } else {
            halt('找不到:' . APP_PATH . $mode . '/controller/' . CONTROLLER . '.php' . ' 控制器');
        }

    }

    /**
     * 初始化
     */
    private static function _init()
    {

        // 设置默认时区
        date_default_timezone_set(C('DEFAULT_TIME_ZONE'));
        
        // 设置编码
        @header('Content-Type: text/html; charset=UTF-8');
        
        session_start();
        
        self::unregisterGlobals();
    }

    /**
     * 删除敏感字符
     * @param $value
     * @return array|string
     */
    private static function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map(array(new self, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    // 检测自定义全局变量并移除
    private static function unregisterGlobals()
    {
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

}
