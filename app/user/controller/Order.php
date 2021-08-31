<?php

namespace app\user\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Curl;

class Order extends Common
{

    public function all()
    {
        return view();
    }

    public function getList()
    {
        $get = $this->get;
        if (!isset($get['rows']) or empty($get['rows'])) {
            $get['rows'] = 10;
        }
        if (!isset($get['page']) or empty($get['page'])) {
            $get['page'] = 1;
        }
        $where = [
            ['uid', '=', $this->user['uid']],
            ['display', '=', 1],
//            ['type', '=', 2]
        ];
        if (isset($get['key']) and !empty($get['key'])) {
            $where[] = ['reallyPrice', 'LIKE', '%' . $get['key'] . '%', 'and'];
            $where[] = ['payId', 'LIKE', '%' . $get['key'] . '%', 'or'];
            $where[] = ['out_trade_no', 'LIKE', '%' . $get['key'] . '%', 'or'];
        }
        $row = Db::name('order')
            ->field('type,out_trade_no,trade_no,price,reallyPrice,createTime,status,commission')
            ->where($where)
            ->limit($get['page'], $get['rows'])
            ->order('oid', 'desc')
            ->select();
        $count = Db::name('order')
            ->field('oid')
            ->where($where)
            ->count();
        $arr = [
            'code' => 0,
            'msg' => '获取成功',
            'data' => $row,
            'count' => $count
        ];
        echo json_encode($arr);
        exit;
    }

    public function del()
    {
        $id = $this->post['out_trade_no'];
        $idArr = explode(',', $id);
        $a = 0;
        foreach ($idArr as $v) {
        	// echo $this->user['uid'];exit;
            $row = Db::name('order')->where([['out_trade_no', '=', $v], ['uid', '=', $this->user['uid']]])->update(['display' => 0]);
            if (!$row) {
                $a = 1;
                break;
            }
        }

        if ($a == 1) {
            return Json::msg(400, '删除失败');
        } else {
            return Json::msg(200, '删除成功');
        }
    }
    
    public function tz() {
    	$out_trade_no = R('post.out_trade_no');
    	if (!isset($out_trade_no) or empty($out_trade_no)) Json::msg(400, '参数错误');
    	$res = Db::name("order")->where('out_trade_no', $out_trade_no)->find();
    	if (!$res) Json::msg(400, '参数错误');
    	
    	$key = $this->user['vKey'];
    	$url = $res['notifyUrl'];
		
		Db::name("tmp_price")->where("out_trade_no", $out_trade_no)->delete();
		
        $time = time();
		$arr = [
			'uid' => $res['uid'],
			'code' => $res['status'],
			'trade_no' => $res['trade_no'],
			'out_trade_no' => $res['out_trade_no'],
			'param' => $res['param'],
			'type' => $res['type'],
			'price' => $res['price'],
			'reallyPrice'=> $res['reallyPrice'],
			'timestamp' => $time
		];

		$arr['sign'] = getSign($arr, $key);
		
        $re = Curl::url($url)->param($arr)->post();
        if (strstr($re, 'ok') or strstr($re, 'OK')) {
            Db::name("order")->where("oid", $res['oid'])->update(["status" => 1]);
            Json::msg(200, '异步通知成功');
        } else {
            // Db::name("order")->where("oid", $res['oid'])->update(["status" => 2]);
            Json::msg(400, '异步通知失败:' . $re);
        }
    	
    }
    
    public function info() {
    	$out_trade_no = R('get.out_trade_no');
    	if (!isset($out_trade_no) or empty($out_trade_no)) Json::msg(400, '参数错误');
    	$res = Db::name("order")->where('out_trade_no', $out_trade_no)->find();
    	if (!$res) Json::msg(400, '参数错误');
    	return view('', ['res' => $res]);
    }

}