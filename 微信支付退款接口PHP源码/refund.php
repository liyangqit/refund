<?php
include './Base.php';
/* 
 * 黎明互联
 * https://www.liminghulian.com/
 */

class Refund extends Base
{   
    const REFUND = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    private $params;
    public function __construct($data) {
        parent::__construct();
        //组装数据
        $this->params = [
            'appid'   =>  self::APPID, //APPID
            'mch_id'  =>  self::MCHID, //商户号
            'nonce_str'=> md5(time()), //随机串
            'sign'  => 'md5',          //签名方式
            'transaction_id'=> $data['transaction_id'],//微信支付订单号 与商户订单号二选一
            //'out_trade_no'=> '', //商户订单号 和微信支付订单号二选一
            'out_refund_no' => $data['out_refund_no'],//退单号
            'total_fee'     => $data['total_fee'],    //订单金额
            'refund_fee'    => $data['refund_fee']    //退款金额
        ];
    }
    //发送退款请求
    public function orderRefund(){
        //生成签名
       $signParams = $this->setSign($this->params);
        //将数据转换为xml
        $xmlData = $this->ArrToXml($signParams);
        //发送请求
       return  $this->postStr(self::REFUND, $xmlData);
    }
    
    //post 字符串到接口
    public function postStr($url,$postfields){
        $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = false; //是否返回响应头信息
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_SSL_VERIFYPEER] = false;//禁用证书校验
	$params[CURLOPT_SSL_VERIFYHOST] = false;
        //以下是证书相关代码
	$params[CURLOPT_SSLCERTTYPE] = 'PEM';
	$params[CURLOPT_SSLCERT] = './cert/apiclient_cert.pem';
	$params[CURLOPT_SSLKEYTYPE] = 'PEM';
	$params[CURLOPT_SSLKEY] = './cert/apiclient_key.pem';
        
        $params[CURLOPT_POSTFIELDS] = $postfields;
        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }
}

$data = [
    'transaction_id'    => '4200000069201801250597519804', //微信交易号
    'out_refund_no'    => '1003', //退款单号
    'total_fee'    => '2', //原订单金额
    'refund_fee'    => '1' //退款金额
];
$obj = new Refund($data);

$res = $obj->orderRefund();

print_r($obj->XmlToArr($res));