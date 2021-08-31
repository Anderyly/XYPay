<?php

include dirname(__FILE__) . '/QPay.php';

$order = time();
$notify = 'http://ay.cc';
$return = 'http://ay.cc';
$aop = new QPay('200489166', 'pydqt7al1p9ve9h2u3o6rfhgj34qvek3', '904', $notify, $return);

/******** 支付开始 ********/
$aop->out_trade_no = $order; // 订单号
$aop->total_amount = 0.01; // 金额
$aop->subject = 'VIP服务'; // 商品描述
$res = $aop->pay(); // 调用支付方法