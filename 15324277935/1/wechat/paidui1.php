<?php
$userid=$_GET["uid"];
$c = new SaeCounter();
$jiao=$c->get('jiao');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">

<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" /><!--固定宽高禁缩放 -->
<meta name="apple-mobile-web-app-capable" content="yes"><!--类webapp-->
<meta name="apple-mobile-web-app-status-bar-style" content="black"><!--状态条 -->
<meta name="format-detection" content="telephone=no"><!--电话号码不为链接 -->

<title>排队主页</title>
<link href="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css" rel="stylesheet" type="text/css"/><!--css链接 -->

<script src="http://code.jquery.com/jquery-1.6.4.min.js" type="text/javascript"></script>
</head>
<script>
function GetQueryString(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}
$(document).ready(function() {
    $("#btn").click(function(){
        var m=$("#cp").val();
        var phoneNum=$("#sj").val();
        //alert(phoneNum);
        $.ajax({
        type:'GET',
            url:'paidui2.php',
            data:{'openId':GetQueryString("uid"), 'carId':m, 'phoneNum':phoneNum},
            success: function(data){
                //$("#cp").val(data);
                alert(data);
            }
        })
    })
        /*
        $("#change").click(function(){
            var m=+$("#cp").val();
            $.ajax({
            type:'GET',
                url:'paidui2.php',
                data:'change='+m,
            })
        })
         */
})
    </script>
<body>
<div data-role="page" id="page"><!--页面 -->
<div data-role="header"><!--标题 -->
<h1>第一站洗车排队处</h1>
</div>
<div data-role="content">        <!--主体 -->
<div>
车牌号：<input name="chepai" id="cp" type="text" value="">
<!-- button id="change">修改</button -->
</div>
<div>
手机号：<input name="phone" id="sj" type="text" value="">
</div>
<div>
<button id="btn">确认</button>
</div>
</div>
</body>
</html>
