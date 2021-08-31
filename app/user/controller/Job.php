<?php

namespace app\user\controller;

use ay\lib\Db;

class Job extends Common
{

    public function index()
    {
        $user = Db::name('user')->field('uid,lastHeart,lastPay,vKey,jobState')->where('uid', $this->user['uid'])->find();
        return view('', ['job' => $user]);
    }

//    public function enQrcode(){
//        $url = $this->get['url'];
//        $qr_code = new QrcodeServer(['generate'=>"display","size",200]);
//        $content = $qr_code->createServer($url);
//        header('Content-type: image/png');
//        echo $content;exit;
//
//    }

}