<?php

namespace app\user\controller;

use ay\lib\Db;
use ay\lib\Json;

class AdminUser extends Common
{
    
    public function __construct() {
        parent::__construct();
        if ($this->user['uid'] != 1) {
            fail('无权访问');
            exit;
        }
    }

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
            ['uid', '!=', 0],
//            ['type', '=', 2]
        ];
        if (isset($get['key']) and !empty($get['key'])) {
            $where[] = ['uid', 'LIKE', '%' . $get['key'] . '%', 'and'];
            $where[] = ['account', 'LIKE', '%' . $get['key'] . '%', 'or'];
            // $where[] = ['out_trade_no', 'LIKE', '%' . $get['key'] . '%', 'or'];
        }
        $row = Db::name('user')
            // ->field('type,out_trade_no,payId,price,reallyPrice,createTime,status,commission')
            ->where($where)
            ->limit($get['page'], $get['rows'])
            ->order('uid', 'desc')
            ->select();
        $count = Db::name('user')
            ->field('uid')
            ->where($where)
            ->count();
            
        foreach ($row as $k => $v) {
            $meal = Db::name("meal_log")->field('mid')->where('uid', $v['uid'])->find();
            if ($meal) {
                $me = Db::name("meal")->field('name,sxf')->where('mid', $meal['mid'])->find();
                $row[$k]['meal'] = $me['name'];
                $row[$k]['sxf'] = $me['sxf'];
            } else {
                $me = Db::name("meal")->field('name,sxf')->where('mid', 1)->find();
                $row[$k]['meal'] = $me['name'];
                $row[$k]['sxf'] = $me['sxf'];
            }
        }
        
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
        $id = $this->post['uid'];
        $idArr = explode(',', $id);
        $a = 0;
        foreach ($idArr as $v) {
        	// echo $this->user['uid'];exit;
            $row = Db::name('user')->where('uid', $v)->delete();
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

    
    public function info() {
    	$uid = R('get.uid');
    	if (!isset($uid) or empty($uid)) Json::msg(400, '参数错误');
    	$res = Db::name("user")->where('uid', $uid)->find();
    	if (!$res) Json::msg(400, '参数错误');
    	$meal = Db::name("meal")->select();
    	return view('', ['res' => $res, 'meal' => $meal]);
    }
    
    public function infoAjax() {
        $data = $this->post;
        if ($data['password'] == '') {
            unset($data['password']);
        } else {
            $data['password'] = user_password($data['password']);
        }
        $mid = $data['meal'];
        unset($data['meal']);
        $uid = $data['uid'];
        unset($data['uid']);
        $meal = Db::name('meal')->field('mid,sxf')->where('mid', $mid)->find();
        if (!$meal) Json::msg(400, '套餐不存在');
        $data['mid'] = $meal['mid'];
        $data['sxf'] = $meal['sxf'];
        
        $meal_log = Db::name('meal_log')->field('Id')->where('uid', $uid)->find();
        if ($meal_log) {
            Db::name('meal_log')->where('uid', $uid)->update(['mid' => $meal['mid']]);
        } else {
            $arr = [
                'mid' => $meal['mid'],
                'uid' => $uid,
                'createTime' => time(),
                'ymd' => date('Ymd', time()),
                'ym' => date('Ym', time()),
                'status' => 1
            ];
            $r = Db::name('meal_log')->insert($arr);
            if (!$r) Json::msg(400, '修改失败');
        }
        // var_dump($data);
        $res = Db::name('user')->where('uid', $uid)->update($data);
        if ($res) {
            Json::msg(200, '修改成功');
        } else {
            Json::msg(400, '修改失败');
        }
    }
    
    public function add() {
        $meal = Db::name("meal")->select();
    	return view('', ['meal' => $meal]);
    }
    
    public function addAjax() {
        $data = $this->post;
        $meal = Db::name('meal')->field('mid,sxf')->where('mid', $data['meal'])->find();
        $row = Db::name('user')->field('uid')->where('account', $data['account'])->find();
        if ($row) Json::msg(400, '该用户已存在');
        $arr = [
            'aid' => 1,
            'account' => $data['account'],
            'password' => user_password($data['password']),
            'mid' => $data['meal'],
            'sxf' => $meal['sxf'],
            'money' => $data['money'],
            'vKey' => md5($data['account'] . $data['password'] . time()),
            'status' => 1,
            'createTime' => time(),
            'ip' => getIp()
        ];
        $row = Db::name('user')->insert($arr);
        $user = Db::name('user')->getLastInsId();
        if ($row) {
            $arr = [
                'mid' => $meal['mid'],
                'uid' => $user[0]['LAST_INSERT_ID()'],
                'createTime' => time(),
                'ymd' => date('Ymd', time()),
                'ym' => date('Ym', time()),
                'status' => 1
            ];
            Db::name('meal_log')->insert($arr);
            Json::msg(200, '添加成功');
        } else {
            Json::msg(400, '添加失败');
        }
    }

}