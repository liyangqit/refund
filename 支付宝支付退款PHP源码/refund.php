<?php
include './Base.php';
/* 
 * 黎明互联
 * https://www.liminghulian.com/
 */

class Refund extends Base
{
    public function oldRefund($batch_num,$detail_data){
        //构建原始数据
        $params = [
            'service' => 'refund_fastpay_by_platform_pwd' , //接口名称
            'partner' => self::PID ,//合作伙伴ID
            '_input_charset' => 'UTF-8' ,//字符集
            'sign_type' => 'MD5' ,//签名方式
            //'seller_email' => '' ,//卖家支付宝账号和合作伙伴ID二选一
            'seller_user_id' => self::PID , //合作伙伴ID
            'refund_date' => date('Y-m-d H:i:s') , //退款请求时间
            'batch_no' => date('Ymd') . '002' ,//退款批次号 格式为：退款日期（8位）+流水号（3～24位）
            'batch_num' => $batch_num , //总笔数
            'detail_data' => $detail_data, //单笔数据集 格式: 支付宝交易号^金额^退款理由
            'notify_url'    => ''//退款通知地址
       ];
        //加入签名
        $signParams = $this->setSign($params);
        //请求接口
        $url = self::PAYGAGEWAY . '?' . $this->getUrl($signParams);
        header("location:" . $url);
    }
    
    public function newrefund(){
        //公共参数 固定值
        $pub_params = [
           'app_id'    => self::APPID,
           'method'    =>  'alipay.trade.refund', //接口名称 应填写固定值alipay.trade.refund
           'format'    =>  'JSON', //目前仅支持JSON
           'charset'    =>  'UTF-8',
           'sign_type'    =>  'RSA2',//签名方式
           'timestamp'    => date('Y-m-d H:i:s'), //发送时间 格式0000-00-00 00:00:00
           'version'    =>  '1.0', //固定为1.0
           'biz_content'    =>  '', //业务请求参数的集合
        ];

        //业务参数
        $api_params = [
           'out_trade_no'  => '20180125174803',//商户订单号 和支付宝交易号trade_no 二选一
           'refund_amount'  => '0.02', //退款金额
           'out_request_no'  => '112', //退款唯一标识  部分退款时必传
        ];
        //公共参数中加入业务参数
        $pub_params['biz_content'] = json_encode($api_params,JSON_UNESCAPED_UNICODE);
        $pub_data = $this->setRsa2Sign($pub_params);
       
       $json_data = $this->curlRequest(self::NEW_PAYGATEWAY,$pub_data);
        echo '<pre>'; 
        print_r(json_decode($json_data,true));
    }
    /**
    使用curl方式实现get或post请求
    @param $url 请求的url地址
    @param $data 发送的post数据 如果为空则为get方式请求
    return 请求后获取到的数据
    */
    public function curlRequest($url,$data = ''){
        $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = false; //是否返回响应头信息
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
		$params[CURLOPT_TIMEOUT] = 30; //超时时间
		if(!empty($data)){
			$params[CURLOPT_POST] = true;
			$params[CURLOPT_POSTFIELDS] = $data;
        }
		$params[CURLOPT_SSL_VERIFYPEER] = false;//请求https时设置,还有其他解决方案
		$params[CURLOPT_SSL_VERIFYHOST] = false;//请求https时,其他方案查看其他博文
        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
		return $content;
    }
}

$obj = new Refund();
//老版
$detail_data = "2018012521001004500506922700^0.01^测试第一笔退款";
//$obj->oldRefund(1, $detail_data);
//新版
$obj->newrefund();