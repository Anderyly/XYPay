<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

/**
 * 跳转函数
 * @param string $str 地址
 * @return null $data 参数
 */
function url($str, $data = null)
{
    return \ay\drive\Url::instance()->get($str, $data);
}

/**
 * 打印函数
 * @param array|string $arr
 */
function dump($arr)
{
    if (is_bool($arr)) {
        var_dump($arr);
    } elseif (is_null($arr)) {
        var_dump(null);
    } else {
        echo "<pre style='padding:10px;border_radius:5px;background:#f5f5f5;border:1px solid #ccc;'>";
        print_r($arr);
        echo "</pre>";
    }
}

/**
 * 打印函数
 * @param string $arr 输出内容
 * @param string $alink 跳转链接
 */
function fail($msg = '页面错误！请稍后再试～', $alink = null)
{
    assign('msg', $msg);
    if (!is_null($alink)) assign('link', $alink);
    view(TEMPLATE . '/fail.html');
    exit;
}

/**
 * 打印函数
 * @param string $msg 输出内容
 * @param string $alink 跳转链接
 */
function success($msg = '操作成功～', $alink = null)
{
    assign('msg', $msg);
    if (!is_null($alink)) assign('link', $alink);
    view(TEMPLATE . '/success.html');
    exit;
}


/**
 * @param null $str
 * @param null $type
 * @return array|mixed|string|string[]|null
 */
function R($str = NULL, $type = null)
{
    if (!strpos($str, '.')) {
        $qm = $str;
        $hm = '';
    } else {
        $hm = substr($str, strripos($str, '.') + 1);
        $qm = substr($str, 0, strrpos($str, '.'));
    }

    switch ($qm) {
        case 'get':
            $data = \ay\lib\Request::get($hm, $type);
            break;
        case 'post':
            $data = \ay\lib\Request::post($hm, $type);
            break;
        case 'url':
            $data = \ay\lib\Request::url();
            break;
        case 'file':
            $data = \ay\lib\Request::file($hm);
            break;
        case 'param':
            $data = \ay\lib\Request::param();
            break;
        case '?get':
            $data = \ay\lib\Request::has($hm, 'get');
            break;
        case '?post':
            $data = \ay\lib\Request::has($hm, 'post');
            break;
        default:
            $data = false;
    }
    return $data;
}

function controller($name, $vae = '')
{
    \ay\lib\Controller::instance()->controller($name, $vae);
}

/**
 * @param string $filename
 * @param null $data
 */
function view($filename = '', $data = null)
{
    \ay\lib\View::view($filename, $data);
}

function assign($name, $value)
{
    \ay\lib\View::assign($name, $value);
}

/**
 * 导入extend下文件
 * @param string $filepath
 * @throws Exception
 */
function extend($filePath)
{
    $filePath = EXTEND . $filePath;
    if (!is_file($filePath)) halt($filePath . ' 不存在');
    include_once $filePath;
}

/**
 * 导入vendor目录下文件
 * @param string $filepath 路径
 * @throws Exception
 */
function vendor($filePath)
{
    $filePath = VENDOR . $filePath;
    if (!is_file($filePath)) halt($filePath . ' 不存在');
    include_once $filePath;
}

/**
 * 全局导入
 * @param string $file 文件名
 * @param array $path 路径
 * @throws Exception
 */
function import($file, $path)
{
    $filePath = $path . $file;
    if (!is_file($filePath)) halt($filePath . ' 不存在');
    include_once $filePath;
}

/**
 * 载入或设置配置顶
 * @param string $name 配置名
 * @param string $value 配置值
 * @return array|string
 */
function C($name = null, $value = null)
{
    static $config = [];
    if (is_null($name)) {
        return $config;
    } elseif (is_string($name)) {
        $name = strtoupper($name);
        $data = array_change_key_case($config, CASE_UPPER);
        if (!strstr($name, '.')) {
            //获得配置
            if (is_null($value)) {
                return isset($data[$name]) ? $data[$name] : null;
            } else {
                return $config[$name] = isset($data[$name]) && is_array($data[$name]) && is_array($value) ? array_merge($config[$name], (array)($value)) : $value;
            }
        } else {
            //二维数组
            $name = array_change_key_case(explode(".", $name));
            if (is_null($value)) {
                return isset($data[$name[0]][$name[1]]) ? $data[$name[0]][$name[1]] : null;
            } else {
                return $config[$name[0]][$name[1]] = $value;
            }
        }
    } elseif (is_array($name)) {
        return $config = array_merge($config, array_change_key_case($name, CASE_UPPER));
    }
}


/**
 * 跳转网址
 * @param string $url 跳转
 * @param int $time 跳转时间
 * @param string $msg
 */
function go($url, $time = 0, $msg = '')
{
    if (!headers_sent()) {
        $time == 0 ? header("Location:" . $url) : header("refresh:{$time};url={$url}");
        exit($msg);
    } else {
        echo "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time) {
            exit($msg);
        }
    }
}

/**
 * 计算脚本运行时间
 * 传递$end参数时为得到执行时间
 * @param string $start 开始标识
 * @param string $end 结束标识
 * @param int $decimals 小数位
 * @return string
 */
function runtime($start, $end = '', $decimals = 3)
{
    static $runtime = [];
    if ($end != '') {
        $runtime [$end] = microtime();
        return number_format($runtime [$end] - $runtime [$start], $decimals);
    }
    $runtime[$start] = microtime();
}

/**
 * HTTP状态信息设置
 * @param Number $code 状态码
 */
function setHttpCode($code)
{
    $state = [
        200 => 'OK', // Success 2xx
        // Redirection 3xx
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',
        // Client Error 4xx
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        // Server Error 5xx
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    ];
    if (isset($state[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $state[$code]);
        header('Status:' . $code . ' ' . $state[$code]);
    }
}

/**
 * 是否为SSL协议
 * @return boolean
 */
function is_ssl()
{
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        return true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true;
    }
    return false;
}

/**
 * 打印常量
 * @return array
 */
function print_const()
{
    $define = get_defined_constants(true);
    foreach ($define['user'] as $k => $d) {
        $const[$k] = $d;
    }
    dump($const);
}

/**
 * 抛出异常
 * @throws Exception
 */

function halt($msg, $file = '', $line = '')
{
    \ay\drive\Error::instance()->init()->halt($msg, $file, $line);
}

/**
 * 无限级分类树
 */
function tree($arr, $id = 'id', $pid = 'pid')
{
    $refer = [];
    $tree = [];
    foreach ($arr as $k => $v) {
        $refer[$v[$id]] = &$arr[$k];
    }
    foreach ($arr as $k => $v) {
        $sid = $v[$pid];
        if ($sid == 0) {
            $tree[] = &$arr[$k];
        } else {
            if (isset($refer[$sid])) {
                $refer[$sid]['children'][] = &$arr[$k];
            }
        }
    }
    return $tree;
}

function lastTime($date, $template)
{
    $s = (time() - $date) / 60;
    switch ($s) {
        case ($s < 60) :
            $msg = intval($s) . '分钟前';
            break;
        case ($s >= 60 && $s < (60 * 24)):
            $msg = intval($s / 60) . '小时前';
            break;
        case ($s >= (60 * 24) and $s < (60 * 24 * 3)) :
            $msg = intval($s / 60 / 24) . '天前';
            break;
        default :
            $msg = date($template, $date);
            break;
    }
    return $msg;
}

// 获取客户端IP地址
function getIp()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] as $xip):
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)):
                $ip = $xip;
                break;
            endif;
        endforeach;
    } else if (isset($_SERVER['HTTP_CLIENT_IP']) and preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) and preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } else if (isset($_SERVER['HTTP_X_REAL_IP']) and preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    return $ip;
}
