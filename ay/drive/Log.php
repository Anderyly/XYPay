<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\drive;

class Log
{

    public static function error($msg, $level = 'ERROR')
    {

        $error = LOG . 'error/';
        if (!is_dir($error)) {
            mkdir($error, 0777, true);
        }

        if (!C('SAVE_ERROR_LOG')) return;
        
        $dest = $error . date('Y_m_d') . '.log';

        if (is_dir($error)) :
            $str = '[Time]: ' . date('Y-m-d H:i:s') . ' [Leve]:' . $level . ' [Message]:' . $msg . ' [IP]:' . getIp() . "\r\n";
            file_put_contents($dest, $str, FILE_APPEND | LOCK_EX);
        endif;

    }

    public static function visit($mode, $controller, $action)
    {
        $error = LOG . 'visit/';
        if (!is_dir($error)) {
            mkdir($error, 0777, true);
        }

        if (!C('SAVE_VISIT_LOG')) return;

        $dest = $error . date('Y_m_d') . '.log';

        if (is_dir($error)) :
            $str = '[Time]: ' . date('Y-m-d H:i:s') . ' [Mode]:' . $mode . ' [Controller]:' . $controller . ' [Action]:' . $action . ' [IP]:' . getIp() . ' [Param]:' . self::getParam() . "\r\n";
            file_put_contents($dest, $str, FILE_APPEND | LOCK_EX);
        endif;

    }

    private static function getParam()
    {
        $content = '';
        $content = json_encode($_SERVER);
        return $content;
    }

}

?>
