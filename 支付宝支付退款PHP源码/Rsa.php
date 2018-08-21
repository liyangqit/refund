<?php

/* 
 * 黎明互联
 * https://www.liminghulian.com/
 */

class RSA
{
    /**
     * RSA签名
     * @param $data 待签名数据
     * @param $private_key 私钥字符串
     * return 签名结果
     */
    function rsaSign($data, $private_key,$type = 'RSA') {

            $search = [
                    "-----BEGIN RSA PRIVATE KEY-----",
                    "-----END RSA PRIVATE KEY-----",
                    "\n",
                    "\r",
                    "\r\n"
            ];

            $private_key=str_replace($search,"",$private_key);
            $private_key=$search[0] . PHP_EOL . wordwrap($private_key, 64, "\n", true) . PHP_EOL . $search[1];
            $res=openssl_get_privatekey($private_key);

            if($res)
            {
                if($type == 'RSA'){
                    openssl_sign($data, $sign,$res);
                }elseif($type == 'RSA2'){
                    //OPENSSL_ALGO_SHA256
                    openssl_sign($data, $sign,$res,OPENSSL_ALGO_SHA256);
                }
                    openssl_free_key($res);
            }else {
                    exit("私钥格式有误");
            }
            $sign = base64_encode($sign);
            return $sign;
    }

    /**
     * RSA验签
     * @param $data 待签名数据
     * @param $public_key 公钥字符串
     * @param $sign 要校对的的签名结果
     * return 验证结果
     */
    function rsaCheck($data, $public_key, $sign,$type = 'RSA')  {
            $search = [
                    "-----BEGIN PUBLIC KEY-----",
                    "-----END PUBLIC KEY-----",
                    "\n",
                    "\r",
                    "\r\n"
            ];
            $public_key=str_replace($search,"",$public_key);
            $public_key=$search[0] . PHP_EOL . wordwrap($public_key, 64, "\n", true) . PHP_EOL . $search[1];
            $res=openssl_get_publickey($public_key);
            if($res)
            {
                if($type == 'RSA'){
                    $result = (bool)openssl_verify($data, base64_decode($sign), $res);
                }elseif($type == 'RSA2'){
                    $result = (bool)openssl_verify($data, base64_decode($sign), $res,OPENSSL_ALGO_SHA256);
                }
                    openssl_free_key($res);
            }else{
                    exit("公钥格式有误!");
            }
            return $result;
    }

}