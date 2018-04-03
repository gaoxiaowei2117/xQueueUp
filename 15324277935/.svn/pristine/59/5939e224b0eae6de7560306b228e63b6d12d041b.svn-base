<?php
//处理GET数据
$appid="wx68e166135164e3a7";//填写AppID
$secret="c0f5baf972995dcd8ae0ba7e5fd05de8";//填写Secret
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//以文件流方式输出
$a=curl_exec($ch);//将文件保存到变量$a
$strjson=json_decode($a);//JSON解析
$access_token = $strjson->access_token;//获取access_token
echo $access_token;
?>
