<?php
 //define your token
 define("TOKEN", "chenxiang");//�ĳ��Լ���TOKEN
 define('APP_ID', '');//�ĳ��Լ���APPID
 define('APP_SECRET', '');//�ĳ��Լ���APPSECRET
 include('wechat.class.php');
 
$wechatObj = new wechatCallbackapiTest(APP_ID,APP_SECRET);
 $wechatObj->Run();
 


 
?>
