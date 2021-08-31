<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2020
 */

namespace app\index\controller;

use ay\lib\Ip;
use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Session;

class Common
{

    public $get;
    public $post;
    public $data;
    public $siteConf;

    // 初始化
    public function __construct()
    {
        $this->data = R('param');
        $this->post = R('post.');
        $this->get = R('get.');
        //
        $res = Db::name('config')->field('k,v')->select();
        $arr = [];
        foreach ($res as $k => $v) {
            $arr[$v['k']] = $v['v'];
        }
        $this->siteConf = $arr;
        //
    }

}