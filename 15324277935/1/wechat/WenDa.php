<?php
/**
 * wechat php test
 */
//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();
class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //extract post data
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr,
                'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $MsgType= $postObj->MsgType;
            $Event= $postObj->Event;
            $EventKey= $postObj->EventKey;
            $time = time();
            $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>0</FuncFlag>
                </xml>";



            $mmc=memcache_init();//初始化缓存
            if($MsgType=="event"){
                if($Event=="CLICK"){
                    if($EventKey=="begin"){
                        memcache_set($mmc,$fromUsername,"1");//第一题
                        $menu=memcache_get($mmc,$fromUsername);
                        $mysql = new SaeMysql();
                        $sql="SELECT * FROM `quiz` where `id`= '{$menu}'";
                        $data = $mysql->getData($sql);
                        $que=$data[0][que];
                        $reply="第1题：".$que;
                    }
                    elseif($EventKey=="1"){
                        $menu=memcache_get($mmc,$fromUsername);
                        if(empty($menu)){
                            $reply="点击开始答题";
                        }
                        else{
                            $mysql = new SaeMysql();
                            $sql="SELECT * FROM `quiz` where `id`= '{$menu}'";
                            $data = $mysql->getData($sql);
                            $ans=$data[0][ans];
                            if($ans==1){
                                $menu=$menu+1;
                                if($menu==11)
                                {
                                    $reply="答对了，题目已结束";
                                    memcache_delete($mmc,$fromUsername);
                                }
                                else{
                                    memcache_set($mmc,$fromUsername,$menu);
                                    $menu=memcache_get($mmc,$fromUsername);
                                    $mysql = new SaeMysql();
                                    $sql="SELECT * FROM `quiz` where `id`= '{$menu}'";
                                    $data = $mysql->getData($sql);
                                    $que=$data[0][que];
                                    $reply="答对了，第".$menu."题：".$que;
                                }
                            }
                            else{
                                $reply="答错了";
                            }
                        }
                    }
                    elseif($EventKey=="2"){
                        $menu=memcache_get($mmc,$fromUsername);
                        if(empty($menu)){
                            $reply="点击开始答题";
                        }
                        else{
                            $mysql = new SaeMysql();
                            $sql="SELECT * FROM `quiz` where `id`= '{$menu}'";
                            $data = $mysql->getData($sql);
                            $ans=$data[0][ans];
                            if($ans==2){
                                $menu=$menu+1;
                                if($menu==11)
                                {
                                    $reply="答对了，题目已结束";
                                    memcache_delete($mmc,$fromUsername);
                                }
                                else{
                                    memcache_set($mmc,$fromUsername,$menu);
                                    $menu=memcache_get($mmc,$fromUsername);
                                    $mysql = new SaeMysql();
                                    $sql="SELECT * FROM `quiz` where `id`= '{$menu}'";
                                    $data = $mysql->getData($sql);
                                    $que=$data[0][que];
                                    $reply="答对了，第".$menu."题：".$que;
                                }
                            }
                            else{
                                $reply="答错了";
                            }
                        }
                    }
                    elseif($EventKey=="3"){
                        $menu=memcache_get($mmc,$fromUsername);
                        if(empty($menu)){
                            $reply="点击开始答题";
                        }
                        else{
                            $mysql = new SaeMysql();
                            $sql="SELECT * FROM `quiz` where `id`= '{$menu}'";
                            $data = $mysql->getData($sql);
                            $ans=$data[0][ans];
                            if($ans==3){
                                $menu=$menu+1;
                                if($menu==11)
                                {
                                    $reply="答对了，题目已结束";
                                    memcache_delete($mmc,$fromUsername);
                                }
                                else{
                                    memcache_set($mmc,$fromUsername,$menu);
                                    $menu=memcache_get($mmc,$fromUsername);
                                    $mysql = new SaeMysql();
                                    $sql="SELECT * FROM `quiz` where `id`= '{$menu}'";
                                    $data = $mysql->getData($sql);
                                    $que=$data[0][que];
                                    $reply="答对了，第".$menu."题：".$que;
                                }
                            }
                            else{
                                $reply="答错了";
                            }
                        }    
                    }
                    else{
                        echo "Input something...";
                    }
                    $msgType = "text";
                    $resultStr = sprintf($textTpl, $fromUsername,$toUsername, $time, $msgType, $reply);
                    echo $resultStr;
                }else {
                    echo "";
                    exit;
                }
            }
        }
    }
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
?>
