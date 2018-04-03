<?php
//GET方式抓取页面
$ch=curl_init(); //初始化
$url="http://www.baidu.com";//设置抓取URL地址
curl_setopt($ch,CURLOPT_URL,$url);//GET方式抓取URL
curl_exec($ch);//执行
curl_close($ch);//关闭
?>
