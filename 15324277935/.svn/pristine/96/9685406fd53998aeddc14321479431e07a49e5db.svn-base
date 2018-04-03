<?php
$mmc=memcache_init();//初始化缓存
$token=memcache_get($mmc,"token");//获取Token
if(empty($token))//判断是否为空，如为空则重新获取Token
{
$appid="wx68e166135164e3a7";
$secret="c0f5baf972995dcd8ae0ba7e5fd05de8";
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$a = curl_exec($ch);
$strjson=json_decode($a);
$access_token = $strjson->access_token;
memcache_set($mmc,"token",$access_token,0,7200);//过期时间为7200秒
$token=memcache_get($mmc,"token");//获取Token
}
//echo $token;
?>