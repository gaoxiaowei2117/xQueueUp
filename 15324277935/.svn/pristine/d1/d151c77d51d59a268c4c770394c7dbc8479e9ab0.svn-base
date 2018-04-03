<?php
//模拟POST请求
$flight="CZ3109";//查询航班号
$post="queryType=flightNum&flightNum={$flight}";  //POST提交内容
$url = "http://eb.csair.com/flightQuery/fltQueryETicketResultN.jsp";
//查询地址
$ch = curl_init();
curl_setopt ($ch, CURLOPT_REFERER, "http://www.csair.com/ "); //模拟来源
curl_setopt($ch, CURLOPT_URL, $url);//URL
curl_setopt($ch, CURLOPT_POST, 1);  //模拟POST
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);//POST内容
curl_exec($ch);
curl_close($ch);
?>