<?php
$time = time()-172800;//当前时间-48小时（172 800秒）
$mysql = new SaeMysql();//sae内部类链接数据库
$sql = "select `openid` FROM `user` WHERE '{$time}'<`time`";
$data = $mysql->getData($sql);
include("token.php");
foreach($data as $openid){
foreach ($openid as $fromUsername){
//构造客服消息的JSON数据
echo $fromUsername;
$contentStr="天天可以群发";
$contentStr=urlencode($contentStr);//转码urlencode，避免JSON转码成Unicode
$a=array("content"=>"{$contentStr}");
$b=array("touser"=>"{$fromUsername}","msgtype"=>"text","text"=>$a);
$post=json_encode($b);
$post=urldecode($post);//解码
$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$token}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_exec($ch);
curl_close($ch);
}
}
?>
