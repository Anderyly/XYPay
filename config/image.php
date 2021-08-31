<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

return [
    // 开关
    'WATER_ON' => true,
    // 水印字体
    'WATER_FONT' => AY . 'data/font/font.ttf',
    // 水印图像
    'WATER_IMG' => AY . 'data/Image/water.png',
    // 位置  1~9九个位置  0为随机
    'WATER_POS' => 9,
    // 透明度
    'WATER_PCT' => 60,
    // 压缩比
    'WATER_QUALITY' => 80,
    // 水印文字
    'WATER_TEXT' => 'vclove.cn',
    // 文字颜色
    'WATER_TEXT_COLOR' => '#f00f00',
    // 文字大小
    'WATER_TEXT_SIZE' => 12,
    // 缩略图前缀
    'THUMB_PREFIX' => '',
    // 缩略图后缀
    'THUMB_ENDFIX' => '_thumb',
    // 生成方式
    // 1:固定宽度,高度自增 2:固定高度,宽度自增 3:固定宽度,高度裁切
    // 4:固定高度,宽度裁切 5:缩放最大边       6:自动裁切图片
    'THUMB_TYPE' => 6,
    // 缩略图宽度
    'THUMB_WIDTH' => 300,
    // 缩略图高度
    'THUMB_HEIGHT' => 300,
];