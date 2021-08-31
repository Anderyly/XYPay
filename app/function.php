<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2018
 */
 
function user_password_auth($tj, $sql) {
    $pass = user_password($tj);
//    echo $pass;exit;
    if ($pass == $sql) {
        return true;
    } else {
        return false;
    }
}

function user_password($password) {
    return '###' . md5(sha1($password . 'AYPHP')) . '###';
}

function getSign($data, $key) {
	ksort($data);
	$str  = '';
	foreach ($data as $k => $v) {
		$str .= $k . '=' . $v . '&';
	}
	return md5($str . 'key=' . $key);
}