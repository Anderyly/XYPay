<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

// 记录开始运行时间
$GLOBALS['_startTime'] = microtime(true);

// 系统常量定义
defined('VERSION') or define('VERSION', '1.3');
defined('AY') or define('AY', dirname(str_replace('\\', '/', __FILE__)) . '/');
defined('ROOT') or define('ROOT', dirname(AY) . '/');
defined('COMMON') or define('COMMON', AY . 'common/');
defined('CONFIG') or define('CONFIG', ROOT . 'config/');
defined('LIB') or define('LIB', AY . 'lib/');
defined('DRIVE') or define('DRIVE', AY . 'drive/');
defined('TEMPLATE') or define('TEMPLATE', AY . 'template/');
defined('TMP') or define('TMP', AY . 'tmp/');
defined('VENDOR') or define('VENDOR', ROOT . 'vendor/');
defined('EXTEND') or define('EXTEND', ROOT . 'extend/');
defined('PUB') or define('PUB', ROOT . 'public/');
defined('TEMP') or define('TEMP', ROOT . 'temp/');
defined('CACHE') or define('CACHE', TEMP . 'cache/');
defined('COMPILE') or define('COMPILE', TEMP . 'compile/');
defined('LOG') or define('LOG', TEMP . 'log/');

$domain = $_SERVER['HTTP_HOST'];
$url = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' . $domain : 'http://' . $domain;
defined('URL') or define('URL', $url);

// 用户目录常量
defined('APP_PATH') or define('APP_PATH', ROOT . APP_NAME . '/');
defined('APP_VIEW') or define('APP_VIEW', APP_PATH . 'view/');
defined('APP_CONTROLLER') or define('APP_CONTROLLER', APP_PATH . 'controller/');
defined('APP_CONFIG') or define('APP_CONFIG', ROOT . 'config/');
defined('APP_COMMON') or define('APP_COMMON', ROOT . 'common/');
defined('APP_MODEL') or define('APP_MODEL', APP_PATH . 'model/');

// 自动加载
if (file_exists(VENDOR . 'autoload.php')) require VENDOR . 'autoload.php';
require DRIVE . 'Autoload.php';
\ay\drive\Autoloader::instance()->init();

require DRIVE . 'Safe.php';
\ay\drive\Safe::instance()->init();

//  加载扩展函数
require AY . 'unity.php';

// 加载默认配置
$configFileArr = scandir(CONFIG);
foreach ($configFileArr as $v) {
    if (!strstr($v, '.php') or $v == '.' or $v == '..') {
        continue;
    }
    C(include CONFIG . $v);
}

// 错误类
error_reporting(0);
if (C('DEBUG')) {
    ini_set('display_errors', 'On');
} else {
    ini_set('display_errors', 'off');
}

\ay\drive\Error::instance()->init();

// 加载配置
$userCommonFile = scandir(APP_CONFIG);
foreach ($userCommonFile as $v) {
    if (!strstr($v, '.php') or $v == '.' or $v == '..') {
        continue;
    }

    C(include APP_CONFIG . $v);
}

// 加载用户自定义函数
if (file_exists(APP_PATH . 'function.php')) {
    require_once APP_PATH . 'function.php';
}

// 框架启动
\ay\Core::run();
