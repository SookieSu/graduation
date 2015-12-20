<?php
/**
  * 用于被动接受微信消息
  */


//define your token
$dir = dirname(__FILE__);
require_once($dir.'/../api/mpApi.php');
require_once($dir.'/../api/findApi.php');

define("TOKEN", "sookiesu");
$wechatObj = new wechatCallbackapiTest();
$retMenu = mpApi::menuCreate();
$wechatObj->responseMsg();
//wechatObj->valid();//用于配置接口

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        //echo $echoStr;
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
                
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $RX_TYPE = trim($postObj->MsgType);
                //echo json_encode($postObj);
                //echo var_dump($postObj);
                switch($RX_TYPE)
                {
                    case "text":
                        $resultStr = $this->handleText($postObj);
                        break;
                    case "event":
                        $resultStr = $this->handleEvent($postObj);
                        break;
                    case "voice":
                        $resultStr = $this->handleVoice($postObj);
                        break;
                    default:
                        $resultStr = "Unknow msg type: ".$RX_TYPE;
                        break;
                }
                echo $resultStr;
        }else {
            echo "";
            exit;
        }
    }

    public function handleText($postObj)
    {
        $keyword = trim($postObj->Content);
        $stateArray = DBMocks::queryStateInfo($postObj->FromUserName);
        if ($stateArray == null)
        {
            $state == MsgType::STATE_BASE;
        }
        if(!empty( $keyword ))
        {
            switch ($stateArray['state']) {
                case MsgType::STATE_BASE:
                    # code...
                    session_id($postObj->FromUserName);
                    session_start();
                    var_dump($_SESSION);
                    if (isset($_SESSION['userID']))
                    {
                        unset($_SESSION['userID']);
                        session_unset();
                        session_destroy();
                    }
                    
                    $contentStr = "普通模式：\n你刚刚说的是："."\n".$postObj->Content."\n"."不过不管你说什么我都不会理你的(￢︿̫̿￢☆)";
                    //for test
                    $resultStr = $this->responseText($postObj,$contentStr);
                    //echo $resultStr;
                    break;
                case MsgType::STATE_SONG:
                    # code...
                    //echo "test before session";
                    session_id($postObj->FromUserName);
                    //$lifetime=60;//保存1分钟
                    //session_set_cookie_params($lifetime);
                    session_start();
                    var_dump($_SESSION);
                    if (!isset($_SESSION['userID']) || $keyword != "添加" )
                    {
                        $_SESSION['userID'] = (string)$postObj->FromUserName;
                        $_SESSION['songName'] = (string)$keyword;
                        $retSongArray = findApi::findSong($keyword);
                        //echo var_dump($retSongArray);
                        $_SESSION['retSongArray'] = (string)json_encode($retSongArray);
                        $resultStr = $this->responseMusic($postObj,$retSongArray['name'],$retSongArray['singer'],$retSongArray['playUrl']);
                        echo $_SESSION['userID'].$_SESSION['songName'];
                    }
                    else
                    {
                        $songName = $_SESSION['songName'];
                        echo $songName;
                        $retSongArray = json_decode($_SESSION['retSongArray'],true);
                        if (DBMocks::addMediaInfo(MsgType::MEDIADATA,$postObj->FromUserName,MsgType::SONG,$retSongArray['link']) == false)
                        {
                            $contentStr = "添加失败噢亲~o(^▽^)o~";
                        }else{
                            $contentStr = "已加入肯德基豪华午餐~\n哔哔，你要的歌已经加入数据库了~";
                        }
                        $resultStr = $this->responseText($postObj,$contentStr);
                    }
                    break;
                case MsgType::STATE_STORY:
                    # code...
                    $contentStr = "你刚刚说的是："."\n".$postObj->Content."\n"."不过不管你说什么我都不会理你的(￢︿̫̿￢☆)";
                    //for test
                    $resultStr = $this->responseText($postObj,$contentStr);
                    //echo $resultStr;
                    break;
                default:
                    # code...
                    $contentStr = "你刚刚说的是："."\n".$postObj->Content."\n"."不过不管你说什么我都不会理你的(￢︿̫̿￢☆)";
                    //for test
                    $resultStr = $this->responseText($postObj,$contentStr);
                    break;
            }
            return $resultStr;
        }else{
            echo "Input something...";
        }
    }

    public function handleEvent($object)
    {
        $contentStr = "";
        switch ($object->Event)
        {
            case MsgType::SUBSCRIBE:
                $contentStr = "感谢您关注SookieSu";
                break;
            case MsgType::CLICK:
                $contentStr = "这是一个点击获取消息的事件：click。\n";
                $boundInfo = mpApi::queryBound($object->FromUserName);
                if ($boundInfo == null)
                {
                    $contentStr = $object->$FromUserName."\n这是一个点击获取消息的事件：click。\n账号未绑定任何设备！\n";
                }else{
                    $state = "";
                    switch($object->EventKey)
                    {
                        case MsgType::SONG_OPEN:
                            $contentStr = "SONG_OPEN\n请输入关键词搜索歌曲，如果需要添加进设备请输入“添加”，退出找歌模式请点击儿歌-》退出";
                            $state = MsgType::STATE_SONG;
                            break;
                        case MsgType::SONG_EXIT:
                            $contentStr = "SONG_EXIT";
                            $state = MsgType::STATE_BASE;
                            break;
                        case MsgType::STORY_OPEN:
                            $contentStr = "STORY_OPEN";
                            $state = MsgType::STATE_STORY;
                            break;
                        case MsgType::STORY_EXIT:
                            $contentStr = "STORY_EXIT";
                            $state = MsgType::STATE_BASE;
                            break;
                        default:
                            $contentStr = "o(^▽^)o";
                            $state = MsgType::STATE_BASE;
                            break;
                    }
                    if (DBMocks::queryStateInfo($object->FromUserName) == null)
                    {
                        DBMocks::addStateInfo($object->FromUserName,$state);
                    }else{
                        DBMocks::updateStateInfo($object->FromUserName,$state);
                    }
                }
                break;
            case MsgType::VIEW:
                $contentStr = "这是一个点击跳转页面的事件：view。\n";
                //echo $object->FromUserName;
                $boundInfo = mpApi::queryBound($object->FromUserName);
                if ($boundInfo == null)
                {
                    $contentStr = "这是一个点击跳转页面的事件：view。\n 账号未绑定任何设备！\n";
                }
                break;
            default :
                $contentStr = "Unknow Event: ".$object->Event;
                break;
        }
        $resultStr = $this->responseText($object, $contentStr);
        return $resultStr;
    }
    public function handleVoice($object)
    {
        //$resultStr = $this->responseVoice($object, $VoiceId);
        $retVoiceData = mpApi::addVoice($object->FromUserName,$object->MediaId);
        $contentStr = $object->ToUserName.":".$object->FromUserName.":".$object->CreateTime.":".$object->MediaId.":".$object->MsgId;
        $resultStr = $this->responseText($object,$contentStr);
        return $resultStr;
    }
    
    public function responseText($object, $content, $flag=0)
    {
        $resultStr = sprintf(MsgType::textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        return $resultStr;
    }
    public function responseMusic($object, $songName,$songDescription,$songUrl)
    {
        $HQurl = "";
        $resultStr = sprintf(MsgType::musicTpl, $object->FromUserName, $object->ToUserName, time(), $songName,$songDescription,$songUrl,$HQurl);
        return $resultStr;
    }

    public function responseVoice($object, $voiceId, $flag=0)
    {
        $resultStr = sprintf(MsgType::voiceTpl, $object->FromUserName, $object->ToUserName, time(), $voiceId, $flag);
        return $resultStr;
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
