<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\drive;

class Safe
{

    public static function instance()
    {
        return new self();
    }

    public function init()
    {
        $referer = empty($_SERVER['HTTP_REFERER']) ? array() : array($_SERVER['HTTP_REFERER']);
        $getFilter = "'|\\b(and|or)\\b.+?(>|<|=|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
        $postFilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
        $cookieFilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
        //$ArrPGC=array_merge($_GET,$_POST,$_COOKIE);
        foreach ($_GET as $key => $value) :
            $this->StopAttack($key, $value, $getFilter);
        endforeach;

        foreach ($_POST as $key => $value) :
            $this->StopAttack($key, $value, $postFilter);
        endforeach;

        foreach ($_COOKIE as $key => $value) :
            $this->StopAttack($key, $value, $cookieFilter);
        endforeach;

        foreach ($referer as $key => $value) :
            $this->StopAttack($key, $value, $getFilter);
        endforeach;
    }

    private function StopAttack($StrFiltKey, $StrFiltValue, $ArrFiltReq)
    {
        $StrFiltValue = $this->arr_foreach($StrFiltValue);
        if (preg_match("/" . $ArrFiltReq . "/is", $StrFiltValue) == 1) {
            $this->slog("<br><br>操作IP: " . $_SERVER["REMOTE_ADDR"] . "<br>操作时间: " . strftime("%Y-%m-%d %H:%M:%S", time()) . "<br>操作页面:" . $_SERVER["PHP_SELF"] . "<br>提交方式: " . $_SERVER["REQUEST_METHOD"] . "<br>提交参数: " . $StrFiltKey . "<br>提交数据: " . $StrFiltValue);
            print "<div style=\"position:fixed;top:0px;width:100%;height:100%;background-color:white;color:green;font-weight:bold;border-bottom:5px solid #999;\"><br>您的提交带有不合法参数,谢谢合作!<br></div>";
            exit();
        }
        if (preg_match("/" . $ArrFiltReq . "/is", $StrFiltKey) == 1) {
            $this->slog("<br><br>操作IP: " . $_SERVER["REMOTE_ADDR"] . "<br>操作时间: " . strftime("%Y-%m-%d %H:%M:%S", time()) . "<br>操作页面:" . $_SERVER["PHP_SELF"] . "<br>提交方式: " . $_SERVER["REQUEST_METHOD"] . "<br>提交参数: " . $StrFiltKey . "<br>提交数据: " . $StrFiltValue);
            print "<div style=\"position:fixed;top:0px;width:100%;height:100%;background-color:white;color:green;font-weight:bold;border-bottom:5px solid #999;\"><br>您的提交带有不合法参数,谢谢合作!<br></div>";
            exit();
        }
    }

    private function slog($logs)
    {
        $path = TEMP . "/log/safe/";
        $toppath = TEMP . "/log/safe/" . date('Y-m-d', time()) . ".htm";
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $Ts = fopen($toppath, "a+");
        fputs($Ts, $logs . "\r\n");
        fclose($Ts);
    }

    private function arr_foreach($arr)
    {
        static $str;
        if (!is_array($arr)) :
            return $arr;
        endif;
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $this->arr_foreach($val);
            } else {
                $str[] = $val;
            }
        }
        return implode($str);
    }
}
