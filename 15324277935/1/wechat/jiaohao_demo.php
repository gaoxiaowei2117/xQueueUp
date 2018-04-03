<?php

header('Content-type:text');

define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    //验证签名
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            echo $echoStr;
            exit;
        }
    }

    //响应消息
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $this->logger("R \r\n".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            //消息类型分离
            switch ($RX_TYPE)
            {
            case "event":
                $result = $this->receiveEvent($postObj);
                break;
            case "text":
                $result = $this->receiveText($postObj);
                break;
            case "image":
                $result = $this->receiveImage($postObj);
                break;
            case "location":
                $result = $this->receiveLocation($postObj);
                break;
            case "voice":
                $result = $this->receiveVoice($postObj);
                break;
            case "video":
            case "shortvideo":
                $result = $this->receiveVideo($postObj);
                break;
            case "link":
                $result = $this->receiveLink($postObj);
                break;
            default:
                $result = "unknown msg type: ".$RX_TYPE;
                break;
            }
            $this->logger("T \r\n".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    //接收事件消息
    private function receiveEvent($object)
    {
        $mysql = new SaeMysql();//SAE内部类链接数据库
        $content = "";
        switch ($object->Event)
        {
        case "subscribe":
            $time = time();
            $sql ="INSERT INTO  `user` (`id` ,`openid` ,`time`)VALUES (NULL ,  '{$object->FromUserName}',  '{$time}')";
            $mysql->runSql($sql);

            //$content = "欢迎使用高晓伟的测试服务号\n请回复以下关键字：文本 表情 单图文 多图文 音乐\n请按住说话 或 点击 + 再分别发送以下内容：语音 图片 小视频 我的收藏 位置";
            //$content .= (!empty($object->EventKey))?("\n来自二维码场景 ".str_replace("qrscene_","",$object->EventKey)):"";
            //$content .= "\n\n".'<a href="http://m.cnblogs.com/?u=txw1958">技术支持 方倍工作室</a>';
            $content = "欢迎使用第一站洗车测试服务号\n";
            break;
        case "unsubscribe":
            $sql = "DELETE FROM `user` WHERE `openid`='{$object->FromUserName}'";
            $mysql->runSql($sql);
            $content = "取消关注";
            break;
        case "CLICK":
            switch ($object->EventKey)
            {
            case "INQUIRE":
                $fromUsername = $object->FromUserName;
                $sql="SELECT * FROM `jiaohao` where `openid`= '{$fromUsername}'";
                $data = $mysql->getData($sql);
                $user=$data[0][openid];
                if (empty($user))
                {

                    //$sql="INSERT INTO `jiaohao` (`id` ,`openid`)VALUES (NULL,'{$fromUsername}')";
                    //$mysql->runSql($sql);
                    //$sql="SELECT * FROM `jiaohao` where `openid` = '{$fromUsername}'";
                    $sql="SELECT max(Id) FROM `jiaohao`";
                    $data = $mysql->getData($sql);
                    $id=$data[0]['max(Id)'];
                    //$id=intval($id);
                    if(empty($id))
                    {
                        $id=0;
                    }
                    else
                    {
                    $id=intval($id);
                    //    $id=intval($data);
                    }
                    $c = new SaeCounter();//读取计数器
                    if ($c->exists('jiao')) {  
                        //$c->incr('jiao');  
                    } else {  
                        $c->create('jiao', 0);  
                        //$c->incr('jiao');  
                    } 
                    $jiao=$c->get('jiao');
                    $num=$id-$jiao;
                    if($num < 0)
                    {
                        $num = 0;
                    }

                    //$description="商家名称：第一站\n排队号码：'请排队'\n\n".date("Y-m-d H:i:s",time());
                    $waitTime=$num*15;
                    $description="商家名称：第一站\n排队号码：-号\n前面等待：".$num."辆\n预计等待：".$waitTime."分钟\n排队状态：未排队\n\n".date("Y-m-d H:i:s",time())."\n\n点击此处进行排队";
                    $content = array();
                    $content[] = array("Title"=>"排号提醒",  "Description"=>$description, "PicUrl"=>"", "Url"=>"http://15324277935.applinzi.com/wechat/paidui1.php?uid=$fromUsername");
                }else{
                    $id=$data[0][id];
                    $id=intval($id);
                    $c = new SaeCounter();//读取计数器
                    if ($c->exists('jiao')) {  
                        //$c->incr('jiao');  
                    } else {  
                        $c->create('jiao', 0);  
                        //$c->incr('jiao');  
                    } 
                    $jiao=$c->get('jiao');
                    $num=$id-$jiao-1;

                    if ($num >= 0)
                    {
                        //$content="你的排号是".$id."\n\n当前是".$jiao."号\n\n前面还有".$num."辆车";
                        $waitTime=$num*15;
                        $description="商家名称：第一站\n排队号码：".$id."号\n前面等待：".$num."辆\n预计等待：".$waitTime."分钟\n排队状态：排队中\n\n".date("Y-m-d H:i:s",time());
                    }else{
                        //$content="你的排号是".$id."\n\n现已开始洗车";
                        $description="商家名称：第一站\n排队号码：".$id."号\n前面等待：-辆\n预计等待：-分钟\n排队状态：已洗车\n\n".date("Y-m-d H:i:s",time());
                    }
                    $content = array();
                    $content[] = array("Title"=>"排号提醒",  "Description"=>$description);
                }
                break;
            case "COMPANY":
                $content = array();
                $content[] = array("Title"=>"方倍工作室", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                break;
            default:
                $content = "点击菜单：".$object->EventKey;
                break;
            }
            break;
        case "VIEW":
            $content = "跳转链接 ".$object->EventKey;
            break;
        case "SCAN":
            $content = "扫描场景 ".$object->EventKey;
            break;
        case "LOCATION":
            $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
            break;
        case "scancode_waitmsg":
            if ($object->ScanCodeInfo->ScanType == "qrcode"){
                $content = "扫码带提示：类型 二维码 结果：".$object->ScanCodeInfo->ScanResult;
            }else if ($object->ScanCodeInfo->ScanType == "barcode"){
                $codeinfo = explode(",",strval($object->ScanCodeInfo->ScanResult));
                $codeValue = $codeinfo[1];
                $content = "扫码带提示：类型 条形码 结果：".$codeValue;
            }else{
                $content = "扫码带提示：类型 ".$object->ScanCodeInfo->ScanType." 结果：".$object->ScanCodeInfo->ScanResult;
            }
            break;
        case "scancode_push":
            $content = "扫码推事件";
            break;
        case "pic_sysphoto":
            $content = "系统拍照";
            break;
        case "pic_weixin":
            $content = "相册发图：数量 ".$object->SendPicsInfo->Count;
            break;
        case "pic_photo_or_album":
            $content = "拍照或者相册：数量 ".$object->SendPicsInfo->Count;
            break;
        case "location_select":
            $content = "发送位置：标签 ".$object->SendLocationInfo->Label;
            break;
        default:
            $content = "receive a new event: ".$object->Event;
            break;
        }

        if(is_array($content)){
            $result = $this->transmitNews($object, $content);
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    //接收文本消息
    private function receiveText($object)
    {
        return $result;
        $keyword = trim($object->Content);
        //多客服人工回复模式
        if (strstr($keyword, "请问在吗") || strstr($keyword, "在线客服")){
            $result = $this->transmitService($object);
            return $result;
        }

        //自动回复模式
        if (strstr($keyword, "文本")){
            $this->updateUserTime($object);
            $content = "这是个文本消息";
        }else if (strstr($keyword, "表情")){
            $content = "微笑：/::)\n乒乓：/:oo\n中国：".$this->bytes_to_emoji(0x1F1E8).$this->bytes_to_emoji(0x1F1F3)."\n仙人掌：".$this->bytes_to_emoji(0x1F335);
        }else if (strstr($keyword, "单图文")){
            $content = array();
            $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
        }else if (strstr($keyword, "图文") || strstr($keyword, "多图文")){
            $content = array();
            $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
        }else if (strstr($keyword, "音乐")){
            $content = array();
            $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://mascot-music.stor.sinaapp.com/zxmzf.mp3", "HQMusicUrl"=>"http://mascot-music.stor.sinaapp.com/zxmzf.mp3"); 
        }else{

            //$reply="你的排号是".$id."当前是".$jiao."号，前面还有".$num."人";
            //$textTpl = "<xml>
            //<ToUserName>$fromUsername</ToUserName>
            //<FromUserName>$toUsername</FromUserName>
            //<CreateTime>$time</CreateTime>
            //<MsgType><![CDATA[text]]></MsgType>
            //<Content>$reply</Content>
            //<FuncFlag>0</FuncFlag>
            //</xml>";
            //echo $textTpl;


            $content = date("Y-m-d H:i:s",time());
        }

        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    //接收图片消息
    private function receiveImage($object)
    {
        return $result;
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        return $result;
        $content = "你发送的是位置，经度为：".$object->Location_Y."；纬度为：".$object->Location_X."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {
        return $result;
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }
        return $result;
    }

    //接收视频消息
    private function receiveVideo($object)
    {
        return $result;
        $content = "上传视频类型：".$object->MsgType;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        return $result;
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        if (!isset($content) || empty($content)){
            return "";
        }

        $xmlTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);

return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return "";
        }
        $itemTpl = "        <item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
            </item>
";
$item_str = "";
foreach ($newsArray as $item){
    $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
}
$xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[news]]></MsgType>
    <ArticleCount>%s</ArticleCount>
    <Articles>
    $item_str    </Articles>
    </xml>";

$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        if(!is_array($musicArray)){
            return "";
        }
        $itemTpl = "<Music>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <MusicUrl><![CDATA[%s]]></MusicUrl>
            <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
            </Music>";

$item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

$xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[music]]></MsgType>
    $item_str
    </xml>";

$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
            <MediaId><![CDATA[%s]]></MediaId>
            </Image>";

$item_str = sprintf($itemTpl, $imageArray['MediaId']);

$xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[image]]></MsgType>
    $item_str
    </xml>";

$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
return $result;
    }

    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
            <MediaId><![CDATA[%s]]></MediaId>
            </Voice>";

$item_str = sprintf($itemTpl, $voiceArray['MediaId']);
$xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[voice]]></MsgType>
    $item_str
    </xml>";

$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
return $result;
    }

    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
            <MediaId><![CDATA[%s]]></MediaId>
            <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            </Video>";

$item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

$xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[video]]></MsgType>
    $item_str
    </xml>";

$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
return $result;
    }

    //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[transfer_customer_service]]></MsgType>
            </xml>";
$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
return $result;
    }

    //回复第三方接口消息
    private function relayPart3($url, $rawData)
    {
        $headers = array("Content-Type: text/xml; charset=utf-8");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    //字节转Emoji表情
    function bytes_to_emoji($cp)
    {
        if ($cp > 0x10000){       # 4 bytes
            return chr(0xF0 | (($cp & 0x1C0000) >> 18)).chr(0x80 | (($cp & 0x3F000) >> 12)).chr(0x80 | (($cp & 0xFC0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else if ($cp > 0x800){   # 3 bytes
            return chr(0xE0 | (($cp & 0xF000) >> 12)).chr(0x80 | (($cp & 0xFC0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else if ($cp > 0x80){    # 2 bytes
            return chr(0xC0 | (($cp & 0x7C0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else{                    # 1 byte
            return chr($cp);
        }
    }

    //日志记录
    private function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 1000000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('Y-m-d H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
    }

    //更行用户互动时间
    private function updateUserTime($object)
    {
        $mysql = new SaeMysql();//SAE内部类链接数据库
        $time = time();
        $sql="UPDATE `user` SET `time`='{$time}'where `openid`='{$object->FromUserName}'";
        $mysql->runSql($sql);
    }
}
?>