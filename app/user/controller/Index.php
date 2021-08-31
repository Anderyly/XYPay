<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2020
 */

namespace app\user\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Session;
use ay\lib\Validator;
use app\service\controller\Geetest;

class Index extends Common
{

    public function index()
    {
        return view();
    }

    public function login()
    {
        return view();
    }
    
    public function reg()
    {
    	if (isset($this->get['aff']) and !empty($this->get['aff'])) {
    		$aff = $this->get['aff'];
    	} else {
    		$aff = 1;
    	}
        return view('', ['aff' => $aff]);
    }
    
    public function checkGeet($data) {
    	$GtSdk = new Geetest($this->siteConf['geetId'], $this->siteConf['geetKey']);
        $gdata = array("user_id" => 'public', "client_type" => "web", "ip_address" => $_SERVER['REMOTE_ADDR']);
        if (Session::get('gtserver') == 1) {
            $result = $GtSdk->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'], $gdata);
            if ($result) {
                $geetest = true;
            } else {
                $geetest = false;
            }
        } else {
            if ($GtSdk->fail_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'])) {
                $geetest = true;
            } else {
                $geetest = false;
            }
        }
        if (!$geetest) return json_encode(['code' => -1, 'msg' => '请先完成滑动验证！']);
    }
    
    public function regAjax() {
    	$data = $this->post;
    	
    	$this->checkGeet($data);
    	
        if ($data['user'] and $data['password'] and $data['repassword'] and $data['aff']) {
        	if (strlen($data['user']) < 6 or strlen($data['password']) < 6) Json::msg(400, '账号密码不能低于6位');
        	if ($data['password'] != $data['repassword']) Json::msg(400, '两次密码输入不一致');
            $res = Db::name('user')->field('uid')->where('account', $data['user'])->find();
            if ($res) Json::msg(400, '账号已存在');
            
            $arr = [
            	'aid' => $data['aff'],
            	'account' => $data['user'],
            	'password' => user_password($data['password']),
            	'mid' => 1,
            	'sxf' => 6,
            	'vKey' => md5($data['user'] . $data['password'] . time()),
            	'status' => 1,
            	'createTime' => time(),
            	'money' => $this->siteConf['defaultMoney'],
            	'ip' => getIp()
            ];
            $res = Db::name('user')->insert($arr);
            if ($res) {
                Json::msg(200, '注册成功');
            } else {
                Json::msg(400, '网络繁忙');
            }
        } else {
            Json::msg(400, '参数请填写完整');
        }
    }

    public function home()
    {
        $user = $this->user;
        $arr = [];
        $arr['todaySuccessOrder'] = Db::name('order')
            ->field('oid')
            ->where([['uid', '=', $user['uid']], ['status', '!=', -1], ['status', '!=', 0], ['ymd', '=', date('Ymd', time())]])
            ->count();
        $arr['todayAllOrder'] = Db::name('order')
            ->field('oid')
            ->where([['uid', '=', $user['uid']], ['ymd', '=', date('Ymd', time())]])
            ->count();
        $arr['todayFailOrder'] = $arr['todayAllOrder'] - $arr['todaySuccessOrder'];
        $arr['todayAllPrice'] = Db::name('order')
            ->field('reallyPrice')
            ->where([['uid', '=', $user['uid']], ['ymd', '=', date('Ymd', time())], ['status', '!=', -1], ['status', '!=', 0]])
            ->sum();
        $arr['allOrder'] = Db::name('order')
            ->field('oid')
            ->where([['uid', '=', $user['uid']]])
            ->count();
        $arr['allPrice'] = Db::name('order')
            ->field('reallyPrice')
            ->where([['uid', '=', $user['uid']], ['status', '!=', -1], ['status', '!=', 0], ])
            ->sum();
        $arr['todayAllPrice'] = empty($arr['todayAllPrice']) ? 0.00 : $arr['todayAllPrice'];
        $arr['allPrice'] = empty($arr['allPrice']) ? 0.00 : $arr['allPrice'];
        
        // 获取所有套餐
        $mealRes = Db::name('meal')->field('mid,name,sxf,price')->select();
        $meal = Db::name('meal')->field('name,sxf')->where('mid', $user['mid'])->find();
        return view('', ['tj' => $arr, 'meal' => $meal, 'mealRes' => $mealRes]);
    }
    
    // 购买套餐
    public function buyMeal() {
    	$mid = R('post.version', 'int');
    	$user = $this->user;
    	if ($user['mid'] == $mid) Json::msg(400, '请勿购买相同套餐');
    	if ($user['mid'] > $mid) Json::msg(400, '请勿降级购买套餐');
    	$meal = Db::name('meal')->field('price')->where('mid', $mid)->find();
    	if (!$meal) Json::msg(400, '套餐不存在');
    	if (bccomp($user['money'], $meal['price'], 2) == -1) Json::msg(400, '金额不足请充值');
    	$money = bcsub($user['money'], $meal['price'], 2);
    	$updateArr = [
    		'mid' => $mid,
    		'money' => $money
    	];
    	$res = Db::name('user')->where('uid', $user['uid'])->update($updateArr);
    	
    	$insertArr = [
    		'uid' => $user['uid'],
    		'mid' => $mid,
    		'createTime' => time(),
    		'ymd' => date('Ymd', time()),
    		'ym' => date('Ym', time()),
    		'status' => 1
    	];
    	$row = Db::name('meal_log')->insert($insertArr);
    	
    	if ($res) {
    		Json::msg(200, '购买成功');
    	} else {
    		Json::msg(400, '网络繁忙');
    	}
    }

    public function pass()
    {
        return view();
    }

    public function set()
    {
        return view();
    }

    public function editPass()
    {
        $data = $this->post;
        if (!isset($data['old_password']) or !isset($data['password']) or !isset($data['confirm_password'])) Json::msg(0, '参数错误');
        if ($data['confirm_password'] != $data['password']) Json::msg(0, '新密码不一致');
        if (!user_password_auth($data['old_password'], $this->user['password'])) Json::msg(0, '原密码错误');
        if (strlen($data['password']) < 6) Json::msg(0, '密码不能低于6位');
        $res = Db::name('user')->where('uid', $this->user['uid'])->update(['password' => user_password($data['password'])]);
        if (!$res) {
            Json::msg(400, '网络繁忙');
        } else {
            Json::msg(200, '修改成功');
        }
    }
    
    public function ajaxUserPost() {
    	
    	$data = R('post.');
    	$checkArr = [
    		// 'weUrl' => 'require|empty',
    		// 'aliUrl' => 'require|empty',
    		'close' => 'require|empty',
    		'vKey' => 'require|empty',
    		'type' => 'require|empty',
    	];
    	Validator::check($data, $checkArr);
    	
    	$updateArr = [
    		'type' => $data['type'],
    		'close' => $data['close'],
    		'vKey' => $data['vKey'],
    		'wxpay' => $data['weUrl'],
    		'zfbpay' => $data['aliUrl']
    	];
    	
    	$row = Db::name('user')->where('uid', $this->user['uid'])->update($updateArr);
    	
    	if ($row) {
    		Json::msg(200, '修改成功');
    	} else {
    		Json::msg(400, '网络繁忙');
    	}
    }

    public function check()
    {
        $data = $this->post;
        
        $this->checkGeet($data);
        
        if ($data['user'] and $data['password']) {
            $res = Db::name('user')
                ->where('account', $data['user'])
                ->find();
            if (!$res) Json::msg(400, '账号不存在');
            if ($res['status'] != 1) Json::msg(400, '账号已被封禁');
            if (user_password_auth($data['password'], $res['password'])) {
                Session::set('user', $res);
                Json::msg(200, '登入成功');
            } else {
                Json::msg(400, '密码错误');
            }
        } else {
            Json::msg(400, '参数请填写完整');
        }
    }
	
    public function logout()
    {
        Session::delete('user');
        go(url('/user/index/index'));
    }

}