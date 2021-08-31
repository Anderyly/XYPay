<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2020
 */

namespace app\index\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Validator;
use ay\lib\Controller;

class Pay extends Controller
{

    public function submit()
    {
        if (!R('get.out_trade_no')) fail('参数错误');
        $res = Db::name('order')->where('out_trade_no', R('get.out_trade_no'))->find();
        if (!$res) fail('参数错误');
        // dump($res);exit;
        $res['payUrl'] = $res['qrCode'];
        return view('', ['res' => $res]);
    }

    // 生成订单号
    private function order($str = '')
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

    //创建订单
    public function createOrder()
    {

        $data = R('post.');

        $checkArr = [
            'uid' => 'require|empty|min:1',
            'out_trade_no' => 'require|empty|max:65',
            'type' => 'require|empty|len:1',
            'price' => 'require|empty|min:1',
            'notify_url' => 'require|empty|min:1',
            'return_url' => 'require|empty|min:1',
            'timestamp' => 'require|empty|len:10',
            'sign' => 'require|empty|len:32',
            'isHtml' => 'require|empty|len:1',
        ];
        Validator::check($data, $checkArr);

//        $job = $this->controller('Job');

        Job::instance()->closeEndOrder($data['uid']);

        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['s']);

        if ($data["price"] <= 0) Json::msg(400, '订单金额必须大于0');

        // 获取用户信息
        $res = Db::name("user")->field('vKey,jobState,type,wxpay,zfbpay,close,money,mid')->where("uid", $data['uid'])->find();
        if ($res['money'] < 0.01) Json::msg(401, '商户额度不足，请充值');
        if (empty($res['wxpay']) and empty($res['zfbpay'])) Json::msg(400, '请您先进入后台配置程序');
        if ($res['jobState'] != "1") Json::msg(400, '监控端状态异常，请检查');
        $closeTime = $res['close'];

        // 签名
        $_sign = getSign($data, $res['vKey']);
        if ($sign != $_sign) Json::msg(400, '签名错误');

        // 获取套餐费率
        $meal = Db::name('meal')->field('sxf')->where('mid', $res['mid'])->find();

        $reallyPrice = bcmul($data['price'], 100);
        $order = $this->order('XY');

        // 金额计算
        $ok = false;
        for ($i = 0; $i < 20; $i++) {
            $tmpPrice = $reallyPrice . "-" . $data['type'] . "-" . $data['uid'];
            $closeTime = time() + $res['close'] * 60;
            $sql = "INSERT IGNORE INTO pay_tmp_price (uid,price,out_trade_no,createTime,closeTime) VALUES (" . $data['uid'] . ",'" . $tmpPrice . "','" . $order . "','" . time() . "','" . $closeTime . "')";
            $row = Db::name('tmp_price')->doExec($sql);

            if ($row) {
                $ok = true;
                break;
            }
            if ($res['type'] == 1) {
                $reallyPrice++;
            } else if ($res['type'] == 2) {
                $reallyPrice--;
            }
        }

        if (!$ok) Json::msg(400, '订单超出负荷，请稍后重试');

        $reallyPrice = bcdiv($reallyPrice, 100, 2);
        // echo $reallyPrice;exit;
        $sxfPrice = bcmul($reallyPrice, $meal['sxf'] / 100, 2);
        if (bccomp($sxfPrice, 0.01, 2) == -1) $sxfPrice = 0.01;
        // echo $sxfPrice;exit;
        if ($sxfPrice > $res['money']) Json::msg(400, '商户额度不足，请充值');

        // 判断支付方式
        switch ($data['type']) {
            case 1 :
                $payUrl = $res['zfbpay'];
                break;
            case 2 :
                $payUrl = $res['wxpay'];
                break;
            default :
                Json::msg(400, '支付方式错误');
        }

        $isAuto = 1;

        // 查询该用户有无固码
        $where = [
            ['uid', '=', $data['uid']],
            ['totalAmount', '=', $reallyPrice],
            ['type', '=', $data['type']]
        ];
        $_payUrl = Db::name("qrcode")->field('url')->where($where)->find();
        if ($_payUrl) {
            $payUrl = $_payUrl['url'];
            $isAuto = 0;
        }

        $res = Db::name("order")->field('oid,status')->where("out_trade_no", $data['out_trade_no'])->find();
        if ($res and $res['status'] == 1) {
            Json::msg(-1, '请勿重复支付');
        }

        // 生成订单数据

        $createDate = time();
        $insertArr = [
            "closeTime" => 0,
            "createTime" => $createDate,
            "is_auto" => $isAuto,
            "notifyUrl" => $data['notify_url'],
            "trade_no" => $order,
            "param" => isset($data['param']) ? $data['param'] : '',
            "payTime" => 0,
            "out_trade_no" => $data['out_trade_no'],
            "qrCode" => $payUrl,
            "price" => $data['price'],
            "reallyPrice" => $reallyPrice,
            "returnUrl" => $data['return_url'],
            "state" => 0,
            "type" => $data['type'],
            'uid' => $data['uid'],
            'ymd' => date('Ymd', time()),
            'commission' => $sxfPrice
        ];
        Db::name("order")->insert($insertArr);

        if ($data['isHtml'] == 1) {
            Json::msg(200, 'success', ['url' => URL . '/pay.action?out_trade_no=' . $data['out_trade_no']]);
        } else {

            $as = [
                'uid' => $data['uid'],
                "out_trade_no" => $data['out_trade_no'],
                // "out_trade_no" => $order,
                "type" => $data['type'],
                "price" => $data['price'],
                "reallyPrice" => $reallyPrice,
                "qrCode" => $payUrl,
                "isAuto" => $isAuto,
                "status" => 0,
                "closeTime" => $closeTime,
                "createTime" => $createDate,
                
            ];
            Json::msg(200, '成功', $as);
        }

    }

    //获取订单信息
    public function getOrder()
    {
        if (R("?post.out_trade_no")) {
            $res = Db::name("order")
                ->field('trade_no,out_trade_no,type,price,reallyPrice,qrCode,is_auto,status,createTime,uid')
                ->where("out_trade_no", R("post.out_trade_no"))
                ->find();
            $row = Db::name("user")->field('close')->where("uid", $res['uid'])->find();

            $data = [
                // "out_trade_no" => $res['payId'],
                "out_trade_no" => $res['out_trade_no'],
                "payType" => $res['type'],
                "price" => $res['price'],
                "reallyPrice" => $res['reallyPrice'],
                "payUrl" => $res['payUrl'],
                "isAuto" => $res['is_auto'],
                "state" => $res['status'],
                "timeOut" => $row['close'],
                "date" => $res['createTime']
            ];
            Json::msg(200, '成功', $data);
        } else {
            Json::msg(-1, '订单号不存在');
        }
    }

    public function orderQuery()
    {
        $data = R('post.');

        $checkArr = [
            'uid' => 'require|empty|min:1',
            'out_trade_no' => 'require|empty|max:65',
            'timestamp' => 'require|empty|len:10',
            'sign' => 'require|empty|len:32',
        ];
        Validator::check($data, $checkArr);

        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['s']);
        // 获取用户信息
        $res = Db::name("user")->field('vKey')->where("uid", $data['uid'])->find();
        // 签名
        $_sign = getSign($data, $res['vKey']);
        if ($sign != $_sign) Json::msg(400, '签名错误');

        $res = Db::name("order")
            ->field('trade_no,out_trade_no,type,price,status,payTime')
            ->where("out_trade_no", R("post.order"))
            ->find();
        if ($res) {
            $data = [
                "out_trade_no" => $res['out_trade_no'],
                "trade_no" => $res['trade_no'],
                "payType" => $res['type'],
                "price" => $res['price'],
                // "reallyPrice" => $res['reallyPrice'],
                // "payUrl" => $res['payUrl'],
                // "isAuto" => $res['is_auto'],
                "status" => $res['status'],
                // "createTime" => $res['createTime'],
                'payTime' => $res['payTime'],
                // 'closeTime' => $res['closeTime']
            ];
            Json::msg(200, 'success', $data);
        } else {
            Json::msg(400, '查无此订单');
        }

    }

    // 查询订单状态
    public function checkOrder()
    {
        $res = Db::name("order")
            ->field('out_trade_no,status,uid,reallyPrice,trade_no,param,type,price,returnUrl')
            ->where("out_trade_no", R("post.out_trade_no"))
            ->find();
        if ($res) {
            if ($res['status'] == 0) Json::msg(-1, "订单未支付");
            if ($res['status'] == -1) Json::msg(-1, "订单已过期");

            $user = Db::name("user")->field('vKey')->where("uid", $res['uid'])->find();

            $res['price'] = number_format($res['price'], 2, ".", "");
            $res['reallyPrice'] = number_format($res['reallyPrice'], 2, ".", "");

            $time = time();
            $arr = [
                'trade_no' => $res['trade_no'],
                'out_trade_no' => $res['out_trade_no'],
                'param' => $res['param'],
                'type' => $res['type'],
                'price' => $res['price'],
                'reallyPrice' => $res['reallyPrice'],
                'timestamp' => $time
            ];
            $str = '';
            ksort($arr);
            foreach ($arr as $k => $v) {
                $str .= $k . '=' . $v . '&';
            }
            $p = $str . 'sign=';
            $sign = md5($str . 'key=' . $user['vKey']);
            $p .= $sign;

            $url = $res['returnUrl'];


            if (strpos($url, "?") === false) {
                $url = $url . "?" . $p;
            } else {
                $url = $url . "&" . $p;
            }

            Json::msg(200, "成功", ['url' => $url]);
        } else {
            Json::msg(-1, "订单号不存在");
        }

    }

}