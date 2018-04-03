<?php
include("token.php");
//include("wx_sample.php");
//include("2-2.php");
$post='{
    "button": [
        {
            "type": "click",
            "name": "今日歌曲",
            "key": "V1001_TODAY_MUSIC"
        },
        {
            "type": "click",
            "name": "歌手简介",
            "key": "V1001_TODAY_SINGER"
        },
        {
            "name": "菜单",
            "sub_button": [
                {
                    "type": "view",
                    "name": "搜索",
                    "url": "http://www.soso.com/"
                },
                {
                    "type": "view",
                    "name": "视频",
                    "url": "http://v.qq.com/"
                },
                {
                    "type": "click",
                    "name": "赞一下我们",
                    "key": "V1001_GOOD"
                }
            ]
        }
    ]
}';//创建菜单
$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$token}";
//$url="http://www.baidu.com";//设置抓取URL地址
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
//curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
//curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
//curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
curl_exec($ch);
curl_close($ch);
?>