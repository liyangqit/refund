<?php
include './Base.php';
/* 
 * 黎明互联
 * https://www.liminghulian.com/
 */

class Notify extends Base
{
    public function __construct() {
        $postData = $_POST;
         $this->logs('refund.txt', print_r($postData,true));
         //验证
         if(!$this->checkSign($postData))
         {
             $this->logs('logno.txt','签名失败');
         }else{
             $this->logs('logno.txt','签名成功');
         }
    }
}

new Notify();