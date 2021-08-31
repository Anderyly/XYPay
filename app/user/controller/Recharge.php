<?php

namespace app\user\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Curl;

class Recharge extends Common
{

    public function submit()
    {
        $money = R('get.money', 'int');
        $type = R('get.type');

        if ($type != 1 and $type != 2) Json::msg(-1, '支付类型有误');
        if ($money < 200) Json::msg(-1, '充值金额要大于200');

        $order = $this->order('M');

        $insertArr = [
            'uid' => $this->user['uid'],
            'out_trade_no' => $order,
            'price' => $money,
            'type' => $type,
            'createTime' => time()
        ];
        $res = Db::name('recharge')->insert($insertArr);
        if (!$res) Json::msg(400, '网络繁忙');


        $host = URL . "/createOrder";
//        echo $host;exit;

        $return_url = URL . '/user/index/index';
        $notify_url = URL . '/user/Notify/recharge';

        // 此uid为当前登入系统的用户
        $param = $this->user['uid'];

        $arr = [
            'uid' => C('uid'),
            'out_trade_no' => $order,
            'param' => $param,
            'type' => $type,
            'price' => $money,
            'return_url' => $return_url,
            'notify_url' => $notify_url,
            'isHtml' => 1,
            'timestamp' => time()
        ];


        $arr['sign'] = getSign($arr, C('vkey'));
        $res = Curl::url($host)->param($arr)->post();
        $res = json_decode($res, true);
        // dump($res);exit;
        if ($res['code'] == 200) {
            go($res['data']['url']);
            exit;
        } else {
            fail($res['msg']);
        }
// 		{
//   "code": 200,
//   "message": "success",
//   "data": {
//     "url": "http://pay.rmc.ink/pay.action?order=XY202003132324307959559026"
//   }
// }
    }

}