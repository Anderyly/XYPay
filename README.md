# 星云免签支付 —— 个人开发者收款解决方案
星云免签支付（PHP）是基于AYPHP1.3 + mysql 实现的一套免签支付程序，主要包含以下特色：

 + 收款即时到账，无需进入第三方账户，收款更安全
 + 提供示例代码简单接入
 + 超简单Api使用，提供统一Api实现收款回调
 + 免费、开源，无后门风险
 + 支持监听店员收款信息，可使用支付宝微信小号/模拟器挂机，方便IOS用户
 + 免root，免xp框架，不修改支付宝/微信客户端，防封更安全

> 监控端已经打包好位于public文件夹下job.apk

> 星云免签的运行环境为PHP版本7.2

> 星云免签仅供个人开发者调试测试使用，请勿用于非法用途，商用请您申请官方商户接口

> bug反馈请建立issues


## 前言

此项目参考[v免签](https://github.com/szvone/vmqphp)开发，由于v免签无法实现多用户化故此开发此源码

## 原理
+ 用户扫码付款 -> 收到款项后手机通知栏会有提醒 -> 星云免签监控端监听到提醒，推送至服务端->服务端根据金额判断是哪笔订单

## 安装
 + 推荐使用宝塔面板安装，以下教程为宝塔面板安装教程，其他环境请参考自行配置

    1、下载源代码,Clone or download->Download ZIP
    
    2、宝塔面板中新建网站，设置：
        
    
        + 网站目录->运行目录 设置为public并保存
        + 伪静态 设置为thinkphp并保存
        + 执行composer install 安装依赖
    
    3、打开网站目录 config/database.php ，设置好您的mysql账号密码。
    
    4、导入数据库文件（位于根目录）sql.sql到您的数据库，并修改pay_config里的极验证，id为geetId字段，key为geetKey字段。
    
    5、至此网站搭建完毕，请访问后自行修改配置信息！默认后台账号为admin 密码为 123456
    
    6、如果您需要重置您的key，需要同时更改config/init.php里面的key以及app/user/controller/Notify.php里面的key
    
    ps: 用户地址既是后台地址 第一个用户为管理员，第六点 key为第一个用户app免签key


## 调用

 + 请部署完成后访问后台，有详细的Api说明

## 版权信息

星云免签遵循 Apache-2.0 License 开源协议发布，并提供免费使用，请勿用于非法用途。


版权所有Copyright © 2020 by anderyly (http://blog.aaayun.cc)

All rights reserved。
