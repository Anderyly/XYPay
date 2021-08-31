<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2020
 */

namespace app\index\controller;

use ay\lib\Db;
use ay\lib\Curl;
use ay\lib\Json;

class Job
{

    public static function instance()
    {
        return new self();
    }

    //获取监控端状态
    public function getState()
    {

        $t = R("get.t");
        $uid = R("get.uid");

        $res = Db::name("user")->field('vKey,lastHeart,lastPay,jobState')->where("uid", $uid)->find();
        $key = $res['vKey'];

        $_sign = $t . $key . $uid;

        if (md5($_sign) != R("get.sign")) Json::msg(-1, '签名校验不通过');

        $data = [
            "lastHeart" => $res['lastHeart'],
            "lastPay" => $res['lastPay'],
            "jobState" => $res['jobState']
        ];
        Json::msg(200, "成功", $data);

    }

    // App心跳接口
    public function appHeart()
    {

        $t = R("get.t");
        $uid = R("get.uid");
        $this->closeEndOrder($uid);
        $res = Db::name("user")->field('vKey')->where("uid", $uid)->find();
        $key = $res['vKey'];

        $_sign = $t . $key . $uid;

        if (md5($_sign) != R("get.sign")) Json::msg(-1, '签名校验不通过');

//        $jg = time()*1000 - $t;
//        if ($jg>50000 || $jg<-50000){
//            return json($this->getReturn(-1, "客户端时间错误"));
//        }

        $updateArr = [
            'lastHeart' => time(),
            'jobState' => 1
        ];

        Db::name("user")->where("uid", $uid)->update($updateArr);
        Json::msg(200, 'success');

    }

    // 关闭过期订单接口
    public function closeEndOrder($uid)
    {
        $res = Db::name("tmp_price")->field('out_trade_no')->where('uid', $uid)->where("closeTime", "<=", time())->select();
        foreach ($res as $v) {
            Db::name("order")
                ->where("status", 0)
                ->where('trade_no', $v['out_trade_no'])
                ->update(["status" => -1, "closeTime" => time()]);
                
            Db::name("tmp_price")
                ->where("out_trade_no", $v['out_trade_no'])
                ->delete();
        }
        
        /*

        $res = Db::name("user")->field('lastHeart,close')->where("uid", $uid)->find();
        if ((time() - $res['lastHeart']) > 60) {
            Db::name("user")->where("uid", $uid)->update(["jobState" => 0]);
        }

        $closeTime = time() - 60 * $res['close'];
        $close_date = time();

        $res = Db::name("order")
            ->where(["createTime", '<=', $closeTime])
            ->where("status", 0)
            ->where('uid', $uid)
            ->update(["status" => -1, "closeTime" => $close_date]);

        $rows = Db::name("order")
            ->field('out_trade_no')
            ->where("status", -1)
            ->where('uid', $uid)
            ->select();
//        $rows[] = ['out_trade_no' => '202003101503222571'];
        foreach ($rows as $row) {
            Db::name("tmp_price")
                ->where("out_trade_no", $row['out_trade_no'])
//                ->where('uid', $uid)
                ->delete();
        }
            */

    }

    // App推送付款数据接口
    public function appPush()
    {

        $t = R("get.t");
        $uid = R("get.uid");
        $type = R('get.type');
        $price = R('get.price');
        $this->closeEndOrder($uid);

        $res = Db::name("user")->field('vKey,money')->where("uid", $uid)->find();
        $key = $res['vKey'];
        $userMoney = $res['money'];

        $_sign = $type . $price . $t . $key . $uid;
        if (md5($_sign) != R("get.sign")) Json::msg(-1, '签名校验不通过');

        $res = Db::name("order")
            ->where('uid', $uid)
            ->where("reallyPrice", $price)
            ->where("status", 0)
            ->where("type", $type)
            ->find();


        if ($res) {

            Db::name("tmp_price")->where("out_trade_no", $res['out_trade_no'])->delete();

            Db::name("order")->where("oid", $res['oid'])->update(["status" => 3, "payTime" => time(), "closeTime" => time()]);

            $money = bcsub($userMoney, $res['commission'], 2);
            Db::name("user")->where("uid", $uid)->update(['money' => $money, 'lastPay' => time()]);

            $url = $res['notifyUrl'];

            $time = time();
            $arr = [
                'uid' => $res['uid'],
                'code' => $res['status'],
                'trade_no' => $res['trade_no'],
                'out_trade_no' => $res['out_trade_no'],
                'param' => $res['param'],
                'type' => $res['type'],
                'price' => $res['price'],
                'reallyPrice' => $res['reallyPrice'],
                'timestamp' => $time
            ];

            $arr['sign'] = getSign($arr, $key);
            $re = Curl::url($url)->param($arr)->post();

            if (strstr($re, 'ok') or strstr($re, 'OK')) {
                Db::name("order")->where("oid", $res['oid'])->update(["status" => 1]);
                Json::msg(200, '异步通知成功');
            } else {
                Db::name("order")->where("oid", $res['oid'])->update(["status" => 2]);
                Json::msg(-1, '异步通知失败');
            }


        } else {
            Db::name("user")->where("uid", $uid)->update(['lastPay' => time()]);
            $data = [
                "closeTime" => 0,
                "createTime" => time(),
                "is_auto" => 0,
                "notifyUrl" => "",
                "out_trade_no" => "无订单转账",
                "param" => "无订单转账",
                "payTime" => 0,
                "trade_no" => "无订单转账",
                "qrCode" => "",
                "price" => $price,
                "reallyPrice" => $price,
                "returnUrl" => "",
                "status" => 1,
                "type" => $type,
                'uid' => 0,
                'ymd' => date('Ymd', time())
            ];

            Db::name("order")->insert($data);
            Json::msg(200, 'success');

        }


    }
}