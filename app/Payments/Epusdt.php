<?php

namespace App\Payments;

use \Curl\Curl;

class Epusdt
{
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function form()
    {
        return [
            'url' => [
                'label' => 'epusdt接口',
                'description' => '',
                'type' => 'input',
            ],
            'key' => [
                'label' => '通讯秘钥',
                'description' => '',
                'type' => 'input',
            ]
        ];
    }

    /**
     * PHP发送Json对象数据
     * @param $url 请求url
     * @param $data 发送的json字符串/数组
     * @return array
     */
    private function json_post($url, $data = NULL)
    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!$data) {
            return 'data is null';
        }
        if (is_array($data)) {
            $data = json_encode($data);
        }
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length:' . strlen($data),
            'Cache-Control: no-cache',
            'Pragma: no-cache'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        $errorno = curl_errno($curl);
        if ($errorno) {
            return $errorno;
        }
        curl_close($curl);
        return $res;

    }

    public function pay($order)
    {
        $parameter = [
            "amount" => $order['total_amount'] / 100,//原价
            "order_id" => $order['trade_no'],
            'redirect_url' => $order['return_url'],
            'notify_url' => $order['notify_url'],
        ];
        $parameter['signature'] = $this->epusdtSign($parameter, $this->config['key']);

        $response = $this->json_post($this->config['url'] . '/api/v1/order/create-transaction', json_encode($parameter));

        $body = json_decode($response, true);

        return [
            'type' => 1, // 0:qrcode 1:url
            'data' => $body['data']['payment_url']
        ];
    }

    private function epusdtSign(array $parameter, string $signKey)
    {
        ksort($parameter);
        reset($parameter); //内部指针指向数组中的第一个元素
        $sign = '';
        $urls = '';
        foreach ($parameter as $key => $val) {
            if ($val == '') continue;
            if ($key != 'signature') {
                if ($sign != '') {
                    $sign .= "&";
                    $urls .= "&";
                }
                $sign .= "$key=$val"; //拼接为url参数形式
                $urls .= "$key=" . urlencode($val); //拼接为url参数形式
            }
        }
        $sign = md5($sign . $signKey);//密码追加进入开始MD5签名
        return $sign;
    }

    public function notify($params)
    {
        $signature = $this->epusdtSign($params, $this->config['key']);

        if ($params['signature'] != $signature) { //不合法的数据
            return 'fail';  //返回失败 继续补单
        } else {
            //合法的数据
            return [
                'trade_no' => $params['trade_id'],
                'callback_no' => $params['order_id']
            ];
        }
    }
}
