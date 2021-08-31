<?php

namespace app\user\controller;

use ay\lib\Db;
use ay\lib\Json;

class Notify {
	
	private $key = 'e6fc78624b135ecd034eb9e56c204a02';

	public function recharge() {
		$data = R('post.');
		// p($data);exit;
		$arr = $data;
		unset($arr['sign']);
		unset($arr['s']);
		$sign = getSign($arr, $this->key);
		if ($data['sign'] != $sign) exit('签名错误');
		
		// 查询订单状态
		$order = Db::name('recharge')->field('status,price,status,uid,rid')->where('out_trade_no', $data['out_trade_no'])->find();
		// 
		$ydOrder = Db::name('order')->field('status,price')->where('out_trade_no', $data['out_trade_no'])->find();
		if ($order['status'] == 0) {
			$user = Db::name('user')->field('money')->where('uid', $order['uid'])->find();
			$money = bcadd($user['money'], $order['price'], 2);
			$row = Db::name('user')->where('uid', $order['uid'])->update(['money' => $money]);
			$row1 = Db::name('recharge')->where('rid', $order['rid'])->update(['status' => 1]);
			if ($row and $row1) exit('OK');
		} else {
			exit('错误');
		}
	}
	
}