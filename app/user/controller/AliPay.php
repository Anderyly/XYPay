<?php

namespace app\user\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Upload;
use Zxing\QrReader;

class AliPay extends Common
{

    public function add()
    {
        return view();
    }

    public function upload()
    {
        $upload = new Upload(PUB . '/upload/', ['jpg', 'jpeg', 'gif', 'png']);
        $res = $upload->operate('file');

        $qrcode = new QrReader($res['path']);  //图片路径
        $text = $qrcode->text(); //返回识别后的文本
        // echo $text;
        if (!strstr(strtolower($text), 'https://qr.alipay.com/')) Json::msg(400, '请上传支付宝收款码');
        $arr = [
            'dz' => URL . '/upload/' . $res['basename'],
            'url' => $text
        ];
        Json::msg(200, 'success', $arr);
    }

    public function ajaxPost()
    {
        $data = $this->post;
        if (isset($data['url']) and !empty($data['url']) and isset($data['total']) and !empty($data['total'])) {
            $arr = [
                'url' => $data['url'],
                'type' => 1,
                'uid' => $this->user['uid'],
                'createTime' => time(),
                'totalAmount' => $data['total']
            ];
            $res = Db::name('qrcode')->insert($arr);
            if ($res) {
                Json::msg(200, 'success');
            } else {
                Json::msg(400, 'error');
            }

        } else {
            Json::msg(400, 'error');
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
            ['uid', '=', $this->user['uid']],
            ['type', '=', 1]
        ];
        if (isset($get['key']) and !empty($get['key'])) {
            $where[] = ['totalAmount', 'LIKE', '%' . $get['key'] . '%'];
        }
        $row = Db::name('qrcode')
            ->field('qid,url,totalAmount')
            ->where($where)
            ->limit($get['page'], $get['rows'])
            ->select();
        $count = Db::name('qrcode')
            ->field('qid')
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
        $id = $this->post['id'];
        $idArr = explode(',', $id);
        $a = 0;
        foreach ($idArr as $v) {
            $row = Db::name('qrcode')->where(['qid', '=', $v], ['uid', '=', $this->user['uid']])->delete();
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


}