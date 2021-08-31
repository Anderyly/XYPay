<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn
 * @copyright Copyright (c) 2018
 */

namespace ay\drive;

use ay\drive\Log;

class Error
{

    public static function instance() {
        return new self();
    }

    public function init() {
        register_shutdown_function([__CLASS__, 'shutdown']);
        set_error_handler([__CLASS__, 'error']);
        return $this;
    }

    // 中止操作
    public static function shutdown() {
        if ($e = error_get_last()) {
            if (in_array($e['type'], array(1, 4))) {
                halt($e['message'], $e['file'], $e['line']);
            }
        }
    }

    // 错误处理
    public static function error($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
                halt($errstr, $errfile, $errline);
                break;
            case E_USER_ERROR:
            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                break;
        }
    }

    public function halt($msg, $file = '', $line = '')
    {
        Log::error($msg . ' [' . $file . '(' . $line . ')]');
        if ($_SERVER['REQUEST_METHOD'] == 'cli') {
            exit($msg);
        } else if (C('DEBUG')) {
            $e['message'] = $msg;
            $e['file'] = $file;
            $e['line'] = $line;
            include_once TEMPLATE . '/error.html';
            exit;
        } else {
            header('HTTP/1.1 500');
            header('Status:500 Internal Server Error');
            include_once TEMPLATE . '/close.html';
            exit;
        }
    }
}