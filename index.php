<?php
 //define your token
 define("TOKEN", "chenxiang");//改成自己的TOKEN
 define('APP_ID', '');//改成自己的APPID
 define('APP_SECRET', '');//改成自己的APPSECRET
 include('wechat.class.php');
 
$wechatObj = new wechatCallbackapiTest(APP_ID,APP_SECRET);
 $wechatObj->Run();
 


 
?>
