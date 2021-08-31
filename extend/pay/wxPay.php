<?php
/**
 * @author anderyly
 * @email anderyly@sina.com
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2018
 */

class WxPay
{

    // 微信分配的公众账号ID
    public $appId;
    // 微信支付分配的商户号
    public $mch_id;
    // 微信支付分配的商户密钥
    public $key;
    // 回调地址
    public $notify_url;
    // 随机字符串
    public $nonce_str;
    // 站点名称
    public $webName;
    // 站点url地址
    public $webUrl;
    // h5支付成功后跳转的地址
    public $redirect_url;

    /**
     * [__construct 初始化配置]
     */
    public function __construct($data)
    {

        $this->appId = $data['appId'];
        $this->mch_id = $data['mch_id'];
        $this->key = $data['key'];
        $this->notify_url = $data['notify_url'];
        if (isset($data['webName'])) $this->webName = $data['webName'];
        if (isset($data['webUrl'])) $this->webUrl = $data['webUrl'];
        if (isset($data['redirect_url'])) $this->redirect_url = $data['redirect_url'];
        $this->nonce_str = $this->rand_code();

    }

    /**
     * [h5 html5支付]
     * @param out_trade_no [订单号]
     * @param $body [商品描述]
     * @param $ip [ip地址]
     * @param $total_fee [订单总金额]
     * @return array|bool
     */
    public function h5($out_trade_no, $body, $ip, $total_fee)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $data = [
            'appid' => $this->appId,
            'mch_id' => $this->mch_id,
            'body' => $body,
            'spbill_create_ip' => $ip,
            'total_fee' => $total_fee * 100,
            'out_trade_no' => $out_trade_no,
            'nonce_str' => $this->nonce_str,
            'notify_url' => $this->notify_url,
            'trade_type' => 'MWEB',
            'scene_info' => "{'h5_info':{'type': 'WAP','wap_url': $this->webUrl,'wap_name': $this->webName}}"
        ];
        $data['sign'] = $this->sign($data); //获取签名
        $data = $this->curlWx($url, $this->ToXml($data));
        if ($data) {
            $res = $this->FromXml($data);
            if (isset($res['return_code']) and $res['return_code'] != 'SUCCESS' and isset($res['return_msg']) and $res['return_msg'] == 'OK' and isset($res['result_code']) and $res['result_code'] == 'SUCCESS') {
                exit("<script>alert('签名失败！');</script>");
            } else {
                $url = $res['mweb_url'] . '&redirect_url=' . urlencode($this->redirect_url);
                 exit('<script src="https://open.mobile.qq.com/sdk/qqapi.js?_bid=152"></script><script type="text/javascript">mqq.ui.openUrl({ target: 2,url: "' . $url . '"});</script>');
            }
        }
    }

    /**
     * [app 移动端二次签名]
     * @param out_trade_no [订单号]
     * @param $body [商品描述]
     * @param $ip [ip地址]
     * @param $total_fee [订单总金额]
     * @return array|bool
     */
    public function app($out_trade_no, $body, $ip, $total_fee)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $data = [
            'appid' => $this->appId,
            'mch_id' => $this->mch_id,
            'body' => $body,
            'spbill_create_ip' => $ip,
            'total_fee' => $total_fee * 100,
            'out_trade_no' => $out_trade_no,
            'nonce_str' => $this->nonce_str,
            'notify_url' => $this->notify_url,
            'trade_type' => 'APP',
        ];
        $data['sign'] = $this->Sign($data);
        $data = $this->curlWx($url, $this->ToXml($data));
        if ($data) {
            $res = $this->FromXml($data);
            if ($res['return_code'] != 'SUCCESS') {
                return false;
            } else {
                $arr = [
                    'prepayid' => $res['prepay_id'],
                    'appid' => $this->appId,
                    'partnerid' => $this->mch_id,
                    'package' => 'Sign=WXPay',
                    'noncestr' => $this->nonce_str,
                    'timestamp' => time(),
                ];
                $sign = $this->sign($arr, 1);
                $arr['out_trade_no'] = $out_trade_no;
                $arr['return_code'] = $res['return_code'];
                $arr['sign'] = $sign;
                return $arr;
            }
        } else {
            return false;
        }
    }
    
    /**
     * [jsapi支付]
     * @param $out_trade_no [订单号]
     * @param $body [商品描述]
     * @param $ip [ip地址]
     * @param $total_fee [订单总金额]
     * @param $openid [授权用户的openid]
     * @return array|bool
     */
    public function jsapi($out_trade_no, $body, $ip, $total_fee, $openid)
    {
        $url = 'https://api2.mch.weixin.qq.com/pay/unifiedorder';
        $data = [
            'appid' => $this->appId,
            'mch_id' => $this->mch_id,
            'body' => $body,
            'spbill_create_ip' => $ip,
            'total_fee' => $total_fee * 100,
            'out_trade_no' => $out_trade_no,
            'nonce_str' => $this->nonce_str,
            'notify_url' => $this->notify_url,
            'trade_type' => 'JSAPI',
            'openid' => $openid
        ];
        $data['sign'] = $this->Sign($data, 1);
        $data = $this->curlWx($url, $this->ToXml($data));
        if ($data) {
            $res = $this->FromXml($data);
            if ($res['return_code'] != 'SUCCESS') {
                return false;
            } else {
                $arr = [
                    'appId' => $res['appid'],
                    'package' => 'prepay_id=' . $res['prepay_id'],
                    'nonceStr' => $this->nonce_str,
                    'timeStamp' => time(),
                    'signType' => 'MD5'
                ];
                $sign = $this->sign($arr);
                $as['nonceStr'] = $arr['nonceStr'];
                $as['package'] = $arr['package'];
                $as['sign'] = $sign;
                $as['timestamp'] = $arr['timeStamp'];
                $as['appid'] = $arr['appId'];
                return $as;
            }
        } else {
            return false;
        }
    }

    /**
     * [curlWx 发送curl请求]
     * @param $url [url地址]
     * @param $xml [xml]
     * @return mixed
     */
    protected function curlWx($url, $xml)
    {
        //header("Content-type:text/xml");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * [sign 签名]
     * @param null $app [不为null开启二次签名]
     * @return string
     */
    protected function sign($params, $app = NULL)
    {
        ksort($params);
        foreach ($params as $key => $item) {
            if (!empty($item)) $newArr[] = $key . '=' . $item;
        }
        $stringA = implode("&", $newArr);
        $stringSignTemp = $stringA . "&key=" . $this->key;
        $sign = MD5($stringSignTemp);
        if (is_null($app)) $sign = strtoupper($sign);
        return $sign;
    }

    /**
     * [FromXml xml转数组]
     * @param $xml
     * @return bool|mixed
     */
    public function FromXml($xml)
    {
        if (!$xml) return false;
        libxml_disable_entity_loader(true);
        $data = @json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

    /**
     * [ToXml 参数组装成xml]
     * @param array $data
     * @return bool|string
     */
    public function ToXml($data = [])
    {
        if (!is_array($data) || count($data) <= 0) return false;
        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * [rand_code 生成随机字符串]
     * @return bool|string
     */
    protected function rand_code()
    {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符
        $str = str_shuffle($str);
        $str = substr($str, 0, 32);
        return $str;
    }

    /**
     * [orderQuery 查询订单]
     * @param $order [商户订单号]
     * @param $money [订单金额]
     * @return bool [false | 成功返回流水号]
     */
    public function orderQuery($order, $money)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $data = array(
            'appid' => $this->appId,
            'mch_id' => $this->mch_id,
            'nonce_str' => $this->nonce_str,
            'out_trade_no' => $order
        );
        $data['sign'] = $this->sign($data);
        $data = $this->curlWx($url, $this->ToXml($data));
        if ($data) {
            $res = $this->FromXml($data);
            if ($res['return_code'] == 'FAIL') {
                return false;
            } else {
                //
                if (isset($res['return_code']) and $res['return_code'] == 'SUCCESS' and isset($res['return_msg']) and $res['return_msg'] == 'OK' and isset($res['result_code']) and $res['result_code'] == 'SUCCESS' and isset($res['trade_state']) and $res['trade_state'] == 'SUCCESS') {
                    //
                    if ($res['total_fee'] == ($money * 100)) {
                        return $res['transaction_id'];
                    } else {
                        return false;
                    }
                    //
                } else {
                    return false;
                }
                //
            }
            //
        } else {
            return false;
        }
    }
}