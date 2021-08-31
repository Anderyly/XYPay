<?php

namespace app\user\controller;

use ay\lib\Ip;
use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Curl;

class AdminMeal extends Common
{

    public function __construct()
    {
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
            ['mid', '!=', 0],
        ];
        if (isset($get['key']) and !empty($get['key'])) {
            $where[] = ['name', 'LIKE', '%' . $get['key'] . '%', 'and'];
        }
        $row = Db::name('meal')
            ->where($where)
            ->limit($get['page'], $get['rows'])
            ->select();
        $count = Db::name('meal')
            ->field('mid')
            ->where($where)
            ->count();

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
        $id = $this->post['mid'];
        $idArr = explode(',', $id);
        $a = 0;
        foreach ($idArr as $v) {
            // echo $this->user['uid'];exit;
            $row = Db::name('meal')->where('mid', $v)->delete();
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


    public function info()
    {
        $mid = R('get.mid');
        if (!isset($mid) or empty($mid)) Json::msg(400, '参数错误');
        $res = Db::name("meal")->where('mid', $mid)->find();
        if (!$res) Json::msg(400, '参数错误');
        return view('', ['res' => $res]);
    }

    public function infoAjax()
    {
        $data = $this->post;
        $mid = $data['mid'];
        unset($data['mid']);
        $res = Db::name('meal')->where('mid', $mid)->update($data);
        if ($res) {
            Json::msg(200, '修改成功');
        } else {
            Json::msg(400, '修改失败');
        }
    }

    public function add()
    {
        return view();
    }

    public function addAjax()
    {
        $data = $this->post;
        $meal = Db::name('meal')->field('mid')->where('name', $data['name'])->find();
        if ($meal) Json::msg(400, '该套餐已存在');

        $row = Db::name('meal')->insert($data);

        if ($row) {
            Json::msg(200, '添加成功');
        } else {
            Json::msg(400, '添加失败');
        }
    }

}