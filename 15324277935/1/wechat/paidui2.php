<?php
$change=$_GET["change"];
$change=intval($change);
$openId=$_GET["openId"];
$carId=$_GET["carId"];
$phoneNum=$_GET["phoneNum"];
if(empty($openId))
{
    echo "系统错误，请稍后重试。";
}
else if(empty($carId) || strlen($carId) != 5)
{
    echo "请输入车牌末五位。";
}
else if(empty($phoneNum) || strlen($phoneNum) != 11)
{
    echo "请输入正确的手机号。";
}
//if(!empty($openId) && !empty($carId) && !empty($phoneNum))
else
{
    $c = new SaeCounter();
    $num=$c->get('jiao');
    $mysql = new SaeMysql();
    $sql = "SELECT * FROM `jiaohao` WHERE `openid` = '{$openId}' and `id`>= '{$num}'";
    $result = $mysql->getdata($sql);
    if(count($result, 0) > 0)
    {
        echo "对不起，暂不支持同一微信多次提交排队信息。";
    }
    else
    {
        $sql ="INSERT INTO  `jiaohao` (`id` ,`openid` ,`carId` ,`phoneNum`)VALUES (NULL , '{$openId}',  '{$carId}', '{$phoneNum}')";
        $mysql->runSql($sql);
        echo "恭喜您，排队成功！";
    }
/*
    $c = new SaeCounter();
    $c->incr('jiao');
    $num=$c->get('jiao');
    echo $num;//加1后返回当前计数
    //发送客服消息
    $mysql = new SaeMysql();
    $sql="SELECT * FROM `jiaohao` where `id`= '{$num}'";
    $data = $mysql->getData($sql);
    $user=$data[0][openid];
    $id=$data[0][id];
    include("token.php");
    $contentStr="现在轮到你了，请尽快和服务员联系";
    $contentStr=urlencode($contentStr);//转码urlencode，避免JSON转码成Unicode
    $a=array("content"=>"{$contentStr}");
    $b=array("touser"=>"{$user}","msgtype"=>"text","text"=>$a);
    $post=json_encode($b);
    $post=urldecode($post);//解码
    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$token}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($ch);
    curl_close($ch);
 */
}
//修改后返回计数
?>
