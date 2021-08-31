<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2019
 */

class AliPay
{
    // 应用ID
    public $appId;

    // 私钥
    public $privateKey;

    // 公钥
    public $publicKey;

    // 异步地址
    public $notify_url;

    // 同步地址
    public $return_url;

    //返回数据格式
    public $format = "json";

    //版本
    public $version = "1.0";

    // 编码
    public $charset = "UTF-8";

    //签名类型
    public $signType = "RSA2";

    // 配置
    public $config;

    // 网关
    public $gatewayUrl = "https://openapi.alipay.com/gateway.do";

    public function __construct($appid, $privateKey, $publicKey, $notify_url, $return_url)
    {
    	$this->appId = $appid;
    	$this->privateKey = $privateKey;
    	$this->publicKey = $publicKey;
    	$this->notify_url = $notify_url;
    	$this->return_url = $return_url;

        $this->config = [
            'alipay_sdk' => 'alipay-sdk-php-20180705',
            'app_id' => $this->appId,
            'notify_url' => $this->notify_url,
            'sign_type' => $this->signType,
            'charset' => $this->charset,
            'timestamp' => date('Y-m-d H:i:s', time()),
            'version' => $this->version
        ];
    }

	/**
     * app支付
     * @param $out_trade_no 商户订单号
     * @param $total_amount 金额
     * @param $subject 标题
     * @return arrray
     */
    public function app($out_trade_no, $total_amount, $subject)
    {
        $method = 'alipay.trade.app.pay';
        $config = $this->config;
        $config['method'] = $method;

        $biz = [
            'total_amount' => $total_amount,
            'product_code' => 'QUICK_MSECURITY_PAY',
            'subject' => $subject,
            'out_trade_no' => $out_trade_no
        ];

        $arr = [
            'subject' => $biz['subject'],
            'out_trade_no' => $out_trade_no,
            'total_amount' => $biz['total_amount'],
            'appid' => $this->appId,
            'notify_url' => $this->notify_url,
            'sign_type' => $this->signType
        ];

        $biz_content = json_encode($biz, JSON_UNESCAPED_UNICODE);

        $config['biz_content'] = $biz_content;
        ksort($config);
        $config['sign'] = $this->sign($config);

        $params_url = '';
        foreach ($config as $k => $v) {
            if (!empty($v)) {
                $params_url .= $k . '=' . urlencode($v) . '&';
            }
        }

        $params_url = rtrim($params_url, '&');
        $arr['sign'] = $params_url;
        return $arr;
    }

	/**
     * 订单查询
     * @param $out_trade_no 
     * @param $total_amount 
     * @return bool|arrray
     */
    public function orderQuery($out_trade_no, $total_amount = '')
    {
        $method = 'alipay.trade.query';
        $config = $this->config;
        $config['method'] = $method;
        unset($config['notify_url']);

        $biz = [
            'out_trade_no' => $out_trade_no
        ];

        $biz_content = json_encode($biz, JSON_UNESCAPED_UNICODE);

        $config['biz_content'] = $biz_content;
        ksort($config);
        $config['sign'] = $this->sign($config);

        $res = $this->curl($config);

        $res = json_decode($res, true);
        $res = $res['alipay_trade_query_response'];

        if ($res['code'] == 10000 and $res['msg'] == 'Success' and $res['trade_status'] == 'TRADE_SUCCESS') {
            if (empty($total_amount)) {
                return $res;
            } else {
                if ($res['total_amount'] == $total_amount) {
                    return $res['trade_no'];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }


    }


    /**
     * curl请求
     * @param $data
     * @return bool|int|mixed|string
     */
    private function curl($data)
    {
        @header("Content-type: text/html;charset=gb2312");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_URL, $this->gatewayUrl);
        if (is_array($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif (is_string($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    /**
     * 签名
     * @param $data
     * @return bool|string
     */
    private function sign($params)
    {
        $stringToBeSigned = "";
        $i = 0;
        
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);

        // 私钥处理
        $res = "-----BEGIN RSA PRIVATE KEY-----" . PHP_EOL . wordwrap($this->privateKey, 64, PHP_EOL, true) . PHP_EOL . "-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        openssl_sign($stringToBeSigned, $sign, $res, OPENSSL_ALGO_SHA256);

        $key = base64_encode($sign);

        return $key;

    }

    private function checkEmpty($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }

}
