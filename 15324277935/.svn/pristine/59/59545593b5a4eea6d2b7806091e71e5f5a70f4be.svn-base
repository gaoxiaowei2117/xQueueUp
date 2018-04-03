<?php
include("token.php");
$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$token}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_exec($ch);
curl_close($ch);
?>
