<?php
$appid="wx68e166135164e3a7";//填写AppID
$secret="c0f5baf972995dcd8ae0ba7e5fd05de8";//填写Secret
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$a = curl_exec($ch);
$strjson=json_decode($a);
$token = $strjson->access_token;//获取Token
$post='
{
"button":[
{
"name":"微洗车",
"sub_button":[
{
"type":"click",
"name":"查询·排队",
"key":"INQUIRE"
},
{
"type":"click",
"name":"绑定·修改",
"key":"BIND_OR_CHAGNE"
},
{
"type":"click",
"name":"更多服务",
"key":"MORE_SERVICE"
}]
}]
}';//创建菜单
$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$token}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_exec($ch);
curl_close($ch);
?>
