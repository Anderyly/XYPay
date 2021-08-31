<?php
/**
 * 文本缓存类
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2019
 */

namespace ay\lib;

use ay\drive\Dir;

class Cache
{

    private $path = TEMP . "cache/data/";

    public static function instance()
    {
        if (!file_exists(TEMP . "cache/data/")) {
            mkdir(TEMP . "cache/data/", 0777, true);
        }
        return new self();
    }

    public function get($name = "")
    {
        if (empty($name)) $name = MODE . "_" . CONTROLLER . "_" . ACTION;
        $path = $this->path . $name . ".txt";
        $fileT = @filemtime($path);
        if (!file_exists($path) or (time() - $fileT) <= C('CACHE_TIME')) {
            return false;
        } else {
            return json_decode(file_get_contents($path), true);
        }
    }

    public function set($name = "", $data = "")
    {
        if (empty($name)) $name = MODE . "_" . CONTROLLER . "_" . ACTION;
        if (!C('CACHE')) {
            return false;
        }
        $path = $this->path . $name . ".txt";
        $data = json_encode($data);
        file_put_contents($path, $data);
        return true;
    }

    public function del($name = "")
    {
        if (empty($name)) $name = MODE . "_" . CONTROLLER . "_" . ACTION;
        $path = $this->path . $name . ".txt";
        unlink($path);
        return true;
    }

    public function delAll()
    {
        return Dir::del($this->path);
    }


}