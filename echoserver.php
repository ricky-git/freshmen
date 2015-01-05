<?php
define("TOKEN", "echo_server");
$signature= $_GET['signature'];
$nonce= $_GET['nonce'];
$timestamp= $_GET['echostr'];
$echostr=$_GET['echostr'];

$trmpArr=array($nonce,$timestamp,TOKEN);
sort($trmpArr);

$tmpStr=impode($trmpArr);
$tmpStr=shal($smpStr);

if($tmpStr==$signature){
	echo $echostr;
}
?>