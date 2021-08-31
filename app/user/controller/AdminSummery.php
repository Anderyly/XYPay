<?php

namespace app\user\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Curl;

class AdminSummery extends Common
{
    
    public function __construct() {
        parent::__construct();
        if ($this->user['uid'] != 1) {
            fail('无权访问');
            exit;
        }
    }

    public function all()
    {
        $key = R('post.key');
        
    	if (R('post.ordertimestart')) $wx[] = ['createTime', '>=', strtotime(R('post.ordertimestart'))];
        if (R('post.ordertimeend')) $wx[] = ['createTime', '<=', strtotime(R('post.ordertimeend'))];
    	if ($key) $whereR[] = ['account', 'like', '%' . $key . '%'];

    	$whereR[] = ['uid', '!=', 0];

    	// 查询所有人
    	$res = Db::name('user')->field('account,uid')->where($whereR)->select();

    	foreach ($res as $k => $v) {

            $where[] = ['uid', '=', $v['uid']];

        	// 总订单

        	if (!isset($wx)) $wx[] = ['oid', '!=', 0];
          	$all = Db::name('order')->field('oid')->where($where)->where($wx)->count();
          	//echo $all;
          	if ($all == 0) {
          		unset($where);
            	unset($res[$k]);
              	continue;
            } else {
            	$res[$k]['all'] = $all;
            }

          	$res[$k]['zfb'] = Db::name('order')->field('oid')->where($where)->where($wx)->where('type', 1)->count();
          	$res[$k]['wx'] = Db::name('order')->field('oid')->where($where)->where($wx)->where('type', 2)->count();
          	$res[$k]['success'] = Db::name('order')->field('oid')->where($where)->where($wx)->where('status', 1)->count();
          	$res[$k]['fail'] = Db::name('order')->field('oid')->where($where)->where($wx)->where('status', '!=', 1)->count();
          	$res[$k]['successPrice'] = Db::name('order')->field('oid')->where($where)->where($wx)->where('status', '=', 1)->sum('reallyPrice');
          	$res[$k]['failPrice'] = Db::name('order')->field('oid')->where($where)->where($wx)->where('status', '!=', 1)->sum('reallyPrice');
          	$where = [];
        }

        return view('', ['res' => $res, 'key' => $key]);
    }

}