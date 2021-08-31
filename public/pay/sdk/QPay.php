<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://blog.aaayun.cc/
 * @copyright Copyright (c) 2019
 */

class QPay {

    // 平台分配的商户号
    private $uid;

    // 商户后台API管理获取
    private $key;

    // 支付类型 1支付宝 2微信
    private $type;

    // 异步地址
    private $notify_url;

    // 同步地址
    private $return_url;

    // 网关
    private $gatewayUrl = "http://pay.vclove.cn";

    // 附带参数
    public $attach = "";

    // 商品描述
    public $subject;

    // 订单号
    public $out_trade_no;

    // 支付金额
    public $total_amount;

    public function __construct($uid, $key, $type = '', $notify_url = '', $return_url = '') {
        $this->uid = $uid;
        $this->key = $key;
        $this->type = $type;
        $this->notify_url = $notify_url;
        $this->return_url = $return_url;
    }

    public function pay() {
        $data = [
            "pay_memberid" => $this->uid,
            "pay_orderid" => $this->out_trade_no,
            "pay_amount" => $this->total_amount,
            "pay_applydate" => date("Y-m-d H:i:s"),
            "pay_bankcode" => $this->bankCode,
            "pay_notifyurl" => $this->notify_url,
            "pay_callbackurl" => $this->return_url,
        ];
        $data['pay_md5sign'] = $this->sign($data);
        $data['pay_attach'] = $this->attach;
        $data['pay_productname'] = $this->subject;

        $str = '';
        foreach ($data as $k => $v) {
            $str .= '<input type="text" hidden name="' . $k . '" value="' . $v . '">' . "\n";
        }
        $url = $this->gatewayUrl . 'Pay_Index.html';
        $query = http_build_query($data);

        $options['http'] = array(
            'timeout' => 60,
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $query
        );
        // $context = stream_context_create($options);
        // $result = @file_get_contents($url, false, $context);
        // var_dump($result);exit;
        $html = <<<eof
			<form action="{$url}" method="post" id="tj">
				{$str}
			</form>
			<script type="text/javascript">
				var form = document.getElementById('tj');
				form.submit();
			</script>
eof;
        echo $html;exit;
    }

    public function orderQuery() {
        $data = [
            'pay_memberid' => $this->uid,
            'pay_orderid' => $this->out_trade_no
        ];
        $data['pay_md5sign'] = $this->sign($data);
        $res = $this->curl($this->gatewayUrl . 'Pay_Trade_query.html', $data);
        $res = json_decode($res, true);
        if (empty($this->total_amount)) {
            return $res;
        } else {
            if ($this->uid == $res['memberid'] and $this->out_trade_no == $res['orderid'] and $res['trade_state'] == 'SUCCESS' and $res['returncode'] == 00 and $this->total_amount == $res['amount']) {
                return $res['transaction_id'];
            } else {
                return false;
            }
        }
    }

    private function curl($url, $httpParams) {
        $ch = curl_init();
        if (stripos($url, 'https://') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置post body
        if (!empty($httpParams)) {
            if (is_array($httpParams)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($httpParams));
            } elseif (is_string($httpParams)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $httpParams);
            }
        }
        // end 设置post body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;

    }

    private function sign($param) {
        ksort($param);
        $str = "";
        foreach ($param as $key => $val) {
            $str = $str . $key . "=" . $val . "&";
        }
        return strtoupper(md5($str . "key=" . $this->key));
    }
}

