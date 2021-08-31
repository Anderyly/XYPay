<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2020
 */

namespace app\user\controller;

use ay\lib\Ip;
use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Session;

class Common
{

    public $get;
    public $post;
    public $data;
    public $user;
    public $siteConf;

    // 初始化
    public function __construct()
    {
        $this->data = R('param');
        $this->post = R('post.');
        $this->get = R('get.');
        $this->auth();
        //
        $res = Db::name('config')->field('k,v')->select();
        $arr = [];
        foreach ($res as $k => $v) {
            $arr[$v['k']] = $v['v'];
        }
        $this->siteConf = $arr;
        assign('siteConf', $this->siteConf);
        //
    }

    private function auth()
    {
        $url = strtolower('/' . MODE . '/' . CONTROLLER . '/' . ACTION);
        // echo $url;exit;
        if (!Session::has('user') and $url != '/user/index/login' and $url != '/user/index/check' and $url != '/user/index/reg' and $url != '/user/index/regajax') go(url('/user/index/login'));
        if (Session::has('user') and $url == '/user/index/login') go(url('/user/index/index'));
        $res = Db::name('user')->where('uid', Session::get('user')['uid'])->find();
        $this->user = $res;
        assign('user', $this->user);
    }
    
    public function order($str = '')
    {
        $order_id_main = $str . date('YmdHis') . mt_rand(10000000, 99999999);
        $order_id_len = strlen($order_id_main);
        $order_id_sum = 0;
        for ($i = 0; $i < $order_id_len; $i++) :
            $order_id_sum += (int)(substr($order_id_main, $i, 1));
        endfor;
        $order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);
        return $order_id;
    }
    
    public function closeOrder($out_trade_no) 
    {
        Db::name("tmp_price")->where('out_trade_no', $out_trade_no)->delete();
    }

}