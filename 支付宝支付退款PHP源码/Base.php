<?php
include './Rsa.php';
/* 
 * 黎明互联
 * https://www.liminghulian.com/
 */

class Base extends RSA
{
    /**
     * 以下信息需要根据自己实际情况修改
     */
    const PID = '';//合作伙伴ID
    const KEY = ''; //安全码
    const PAYGAGEWAY = 'https://mapi.alipay.com/gateway.do';
    const CHECKURL = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner=' . self::PID . '&notify_id=';
    const APPPRIKEY = '';//私钥
    const APPID = ''; //appid
    const NEW_ALIPUBKE = '';//公钥
    const NEW_PAYGATEWAY = 'https://openapi.alipay.com/gateway.do';

    public function getStr($arr,$type = 'RSA'){
        //筛选  
        if(isset($arr['sign'])){
            unset($arr['sign']);
        }
        if(isset($arr['sign_type']) && $type == 'RSA'){
            unset($arr['sign_type']);
        }
        //排序  
        ksort($arr);
        //拼接
       return  $this->getUrl($arr,false);
    }
    //将数组转换为url格式的字符串
    public function getUrl($arr,$encode = true){
       if($encode){
            return http_build_query($arr);
       }else{
            return urldecode(http_build_query($arr));
       }
    }
    //获取签名MD5
    public function getSign($arr){
       return  md5($this->getStr($arr) . self::KEY );
    }
    //获取含有签名的数组MD5
    public function setSign($arr){
        $arr['sign'] = $this->getSign($arr);
        return $arr;
    }
    //获取签名RSA
    public function getRsaSign($arr){
       return $this->rsaSign($this->getStr($arr), self::APPPRIKEY) ;
    }
    //获取含有签名的数组RSA
    public function setRsaSign($arr){
        $arr['sign'] = $this->getRsaSign($arr);
        return $arr;
    }
    //获取签名RSA2
    public function getRsa2Sign($arr){
       return $this->rsaSign($this->getStr($arr,'RSA2'), self::APPPRIKEY,'RSA2') ;
    }
    //获取含有签名的数组RSA
    public function setRsa2Sign($arr){
        $arr['sign'] = $this->getRsa2Sign($arr);
        return $arr;
    }
    //记录日志
    public function logs($filename,$data){
        file_put_contents('./logs/' . $filename, $data . "\r\n",FILE_APPEND);
    }
    //2.验证签名
    public function checkSign($arr){
        $sign = $this->getSign($arr);
        if($sign == $arr['sign']){
            return true;
        }else{
            return false;
        }
    }
     
    //验证是否来之支付宝的通知
    public function isAlipay($arr){
        $str = file_get_contents(self::CHECKURL . $arr['notify_id']);
        if($str == 'true'){
            return true;
        }else{
            return false;
        }
    }
    // 4.验证交易状态
    public function checkOrderStatus($arr){
        if($arr['trade_status'] == 'TRADE_SUCCESS' || $arr['trade_status'] == 'TRADE_FINISHED'){
            return true;
        } else {
            return false;
        }
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