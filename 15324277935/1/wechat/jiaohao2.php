<?php
// 指定允许其他域名访问  
header('Access-Control-Allow-Origin:*');  
// 响应类型  
header('Access-Control-Allow-Methods:*');  
// 响应头设置  
header('Access-Control-Allow-Headers:x-requested-with,content-type');  
$id=$_GET["id"];
$change=$_GET["change"];
$change=intval($change);
if($id=="1"){
    $c = new SaeCounter();
    //$c->incr('jiao');
    $num=$c->get('jiao');
    //发送客服消息
    //echo $num;//加1后返回当前计数
    //$mysql = new SaeMysql();

    $servername = "w.rdc.sae.sina.com.cn";
    $username = "x01nx3l3o1";
    $password = "lm44l12wjxxy3mmlkxj4zwy3ymzl33h01z2j3ihx";
    $databasename = "app_15324277935";

    // 创建连接
    $mysql = new mysqli($servername, $username, $password, $databasename);

    // 检测连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    } 

    $sql="SELECT id,carId,phoneNum FROM `jiaohao`";
    $result = $mysql->query($sql);
    if($result->num_rows > 0)
    {
        //打印全部排队信息
        while($row = mysqli_fetch_assoc($result)) 
        {
            $dataResult[] = $row;
        }
        echo json_encode($dataResult);
    }
    else
    {
        echo '[]';
    }
    $mysql->close();
}
elseif($id=="2")
{
    $c = new SaeCounter();
    $c->incr('jiao');
    $num=$c->get('jiao');
    //发送客服消息
    //echo $num;//加1后返回当前计数
    //$mysql = new SaeMysql();

    $servername = "w.rdc.sae.sina.com.cn";
    $username = "x01nx3l3o1";
    $password = "lm44l12wjxxy3mmlkxj4zwy3ymzl33h01z2j3ihx";
    $databasename = "app_15324277935";

    // 创建连接
    $mysql = new mysqli($servername, $username, $password, $databasename);

    // 检测连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    } 

    //处理发送数据逻辑
    $sql="SELECT * FROM `jiaohao` where `id`>= '{$num}'";
    $account=0;
    $result = $mysql->query($sql);
    if($result->num_rows > 0)
    {
        //while(!empty($sql[$account]))
        //echo $result->num_rows;
        while($row = mysqli_fetch_assoc($result)) 
        {
            $data[] = $row;
        }
        //$date = $data->fetch_all();
        while(count($data,COUNT_NORMAL) > $account && $account <= 3)
        {
            $user=$data[$account][openid];
            $id=$data[$account][id];
            include("token.php");
            if(0 == $account)
            { 
                $contentStr="现在轮到你了，请尽快和服务员联系";
                //echo $data[$account][carId];
                echo json_encode($data[$account]);
            }
            else
            {
                $contentStr="前面还有".$account."辆车";
            }
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
            //$num++;
            $account++;
            //$sql="SELECT * FROM `jiaohao` where `id`= '{$num}'";
        }
        //$data = $mysql->query($sql);
    }
    else
    {
        $num = $num - 1;
        $c->set('jiao',$num);
        $sql="SELECT * FROM `jiaohao` where `id` = '{$num}'";
        $result = $mysql->query($sql);
        if($result->num_rows > 0)
        {
            while($row = mysqli_fetch_assoc($result)) 
            {
                echo json_encode($row);
            }
        }
        else
        {
            echo '{"id":"","openid":"","carId":"","phoneNum":""}';
        }
    }
    //echo ";";
    //echo $num;
    $mysql->close();
}
elseif($id=="3")
{
    $c = new SaeCounter();
    $num=$c->get('jiao');

    $servername = "w.rdc.sae.sina.com.cn";
    $username = "x01nx3l3o1";
    $password = "lm44l12wjxxy3mmlkxj4zwy3ymzl33h01z2j3ihx";
    $databasename = "app_15324277935";

    // 创建连接
    $mysql = new mysqli($servername, $username, $password, $databasename);

    // 检测连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    } 

    $sql="SELECT * FROM `jiaohao` where `id` = '{$num}'";
    $result = $mysql->query($sql);
    if($result->num_rows > 0)
    {
        while($row = mysqli_fetch_assoc($result)) 
        {
            echo json_encode($row);
        }
    }
    else
    {
        echo '{"id":"","openid":"","carId":"","phoneNum":""}';
    }
    $mysql->close();
}
if(!empty($change)){
    $c = new SaeCounter();
    $c->set('jiao',$change);
}
//修改后返回计数
?>

