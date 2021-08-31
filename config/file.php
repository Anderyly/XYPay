<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

return [
    // 储存驱动 支持File与Memcache储存
    'STORAGE_DRIVER' => 'File',
    // 上传图片缩略图处理
    'UPLOAD_THUMB_ON' => false,
    // 允许上传类型
    'UPLOAD_ALLOW_TYPE' => [
        'jpg', 'jpeg', 'gif', 'png'
    ],
    // 允许上传文件大小 单位B
    'UPLOAD_ALLOW_SIZE' => 2097152,
    // 上传路径
    'UPLOAD_PATH' => PUB . 'Upload/',
];