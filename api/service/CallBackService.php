<?php
/**
  * 用于被动接受微信消息
  */


//define your token
$dir = dirname(__FILE__);
require_once($dir.'/../api/mpApi.php');

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
                echo var_dump($postObj);
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
        if(!empty( $keyword ))
        {
            $contentStr = "你刚刚说的是："."\n".$postObj->Content."\n"."不过不管你说什么我都不会理你的(￢︿̫̿￢☆)";
            //for test
            $resultStr = $this->responseText($postObj,$contentStr);
            echo $resultStr;
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
                $boundInfo = mpApi::queryBound($object->$FromUserName);
                if ($boundInfo == null)
                {
                    $contentStr = "这是一个点击获取消息的事件：click。\n账号未绑定任何设备！\n";
                }
                break;
            case MsgType::VIEW:
                $contentStr = "这是一个点击跳转页面的事件：view。\n";
                echo $object->$FromUserName;
                $boundInfo = mpApi::queryBound($object->$FromUserName);
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
