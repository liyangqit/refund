<?php

/* 
 * 黎明互联
 * https://www.liminghulian.com/
 */

class Base
{
    const KEY = ''; //支付秘钥需要更改成自己的
    const APPID = ''; //APPID需要更改为自己的
    const MCHID = ''; //商户号需要更改成自己的
    const SECRET = ''; //开发者密码需要更改为自己的
    const UOURL = 'https://api.mch.weixin.qq.com/pay/unifiedorder'; //无需更改 统一下单API地址
    const NOTIFY = '';   //支付通知地址需要更改成你自己服务器的地址

    public function __construct() {
       
    }
    //获取签名
    public function getSign($arr){ 
        //去除数组的空值
        array_filter($arr);
        if(isset($arr['sign'])){
            unset($arr['sign']);
        }
        //排序
        ksort($arr);
        //组装字符
        $str = $this->arrToUrl($arr) . '&key=' . self::KEY;
        //使用md5 加密 转换成大写 
       return strtoupper(md5($str));
    }
    //获取带签名的数组
    public function setSign($arr){
        $arr['sign'] = $this->getSign($arr);
        return $arr;
    }
    //校验签名
    public function checkSign($arr){        
        //生成新签名
        $sign = $this->getSign($arr);
        //和数组中原始签名比较
        if($sign == $arr['sign']){
            return true;
        }else{
            return false;
        }
    }
    //数组转URL字符串 不带key
    public function arrToUrl($arr){
        return urldecode(http_build_query($arr));
    }
    //记录到文件
    public  function logs($file,$data){
        $data = is_array($data) ? print_r($data,true) : $data;
        file_put_contents('./logs/' .$file, $data);
    }
    public function getPost(){
        return file_get_contents('php://input');
    }
    //Xml 文件转数组
    public function XmlToArr($xml)
    {	
        if($xml == '') return '';
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
        return $arr;
    }
    //数组转XML
    public function ArrToXml($arr)
    {
        if(!is_array($arr) || count($arr) == 0) return '';

        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
                if (is_numeric($val)){
                        $xml.="<".$key.">".$val."</".$key.">";
                }else{
                        $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
                }
        }
        $xml.="</xml>";
        return $xml; 
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
        $params[CURLOPT_POSTFIELDS] = $postfields;
        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }
  
}