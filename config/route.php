<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2018
 */

return [
    '^pay\.action.*?$' => 'index/Pay/submit',
    '^enQrcode.*?$' => 'index/index/enQrcode',
    '^getOrder.*?$' => 'index/pay/getOrder',
    '^checkOrder.*?$' => 'index/pay/checkOrder',
    '^createOrder.*?$' => 'index/pay/createOrder',
    '^appHeart.*?$' => 'index/job/appHeart',
    '^appPush.*?$' => 'index/job/appPush',
    '^orderQuery.*?$' => 'index/pay/orderQuery',
    '^doc.*?$' => 'index/index/doc',
    '^demo.*?$' => 'index/index/demo',
    '^submit$' => 'index/index/submit',
    '^geet.*?$' => 'index/index/geet',
    '^returnUrl.*?$' => 'index/index/returnUrl',
//    '^user\/(\d+)$' => 'User/User/getUserById/id/$1',
//    '^user\/(\d+)\/article$' => 'User/User/getUserArticle/uid/$1',
];