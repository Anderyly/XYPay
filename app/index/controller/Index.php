<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2020
 */

namespace app\index\controller;

use ay\lib\Curl;
use ay\lib\Validator;
use ay\lib\Session;
use ay\drive\Dir;
use app\service\controller\Geetest;

class Index extends Common
{
	
	public function index() {
		$siteConf = $this->siteConf;
		return view('', ['siteConf' => $siteConf]);
	}
	
	public function demo() {
		$siteConf = $this->siteConf;
		return view('', ['siteConf' => $siteConf]);
	}
	
	public function submit() {
		$data = $this->post;
		$checkArr = [
        	'orderid' => 'require|empty|max:65',
        	'type' => 'require|empty|len:1',
        	'price' => 'require|empty|min:1',
        ];
		Validator::check($data, $checkArr);
		$order = $data['orderid'];
		$price = $data['price'];
		$type = $data['type'];
		$param = '测试';
		$host = URL . "/createOrder";
		$return_url =  URL . '/returnUrl';
		$notify_url =  URL . '/index/index/notifyUrl';
		$arr = [
			'uid' => C('uid'),
			'out_trade_no' => $order,
			'param' => $param,
			'type'=> $type,
			'price' => $price,
			'return_url' => $return_url,
			'notify_url' => $notify_url,
			'isHtml' => 1,
			'timestamp' => time()
		];
		$arr['sign'] = getSign($arr, C('vkey'));
		$res = Curl::url($host)->param($arr)->post();
		$res = json_decode($res, true);
		if ($res['code'] == 200) {
			go($res['data']['url']);
			exit;
		} else {
			fail($res['msg']);
		}
	}
	
	public function notifyUrl() {
		$data = $this->post;
// 		dump($data);exit;
		$arr = $data;
		unset($arr['sign']);
		unset($arr['s']);
		$sign = getSign($arr, C('vkey'));
		if ($data['sign'] != $sign) exit('签名错误');
		exit('ok');
	}
	
	public function returnUrl() {
		$data = $this->get;
		// p($data);exit;
		$arr = $data;
		unset($arr['sign']);
		unset($arr['s']);
		$sign = getSign($arr, C('vkey'));
		if ($data['sign'] != $sign) exit('签名错误');
		exit('ok');
	}
	
	public function doc() {
		$siteConf = $this->siteConf;
		return view('', ['siteConf' => $siteConf]);
	}
	
    public function enQrcode()
    {

        $text = R('get.url');
        $path = PUB . '/upload/qr/';
        $file = md5($text . time()) . '.png';
        if (!is_dir($path)) Dir::create($path);
        extend('QRcode.php');
        \QRcode::png($text, $path . $file, QR_ECLEVEL_L, 5, 1, true);
        header('Content-Type: image/png');
        $img = imagecreatefrompng($path . $file);
        imagepng($img);
        imagedestroy($img);

    }
    
    public function geet()
    {
        $GtSdk = new Geetest($this->siteConf['geetId'], $this->siteConf['geetKey']);
        $data = array("user_id" => 'public', "client_type" => "web", "ip_address" => $_SERVER['REMOTE_ADDR']);
        $status = $GtSdk->pre_process($data, 1);
        Session::set('gtserver', $status);
        Session::set('user_id', 'public');
        //session('gtserver', $status);
        // session('user_id', 'public');
        echo $GtSdk->get_response_str();
    }

}