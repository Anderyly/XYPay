<?php

include_once dirname(__FILE__) . '/qPay.php';

$key = '密钥';

$data = $arr = $_POST;
unset($arr['sign']);
$sign = getSign($arr, $key);
if ($data['sign'] != $sign) exit('签名错误');
//处理你自己的业务 处理成功记得返回 ok