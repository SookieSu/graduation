<?php
/**
  * wechat php test
  */
//include 'wx_tpl.php';
$dir = dirname(__FILE__);
require($dir.'/wx_tpl.php');

//define your token
define("TOKEN", "sookiesu");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();
//$wechatObj->valid();//用于配置接口

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
                
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $RX_TYPE = trim($postObj->MsgType);

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
            case "subscribe":
                $contentStr = "感谢您关注SookieSu"."\n"."你关注了也没有什么卵用0-0，因为这是我做毕设用的0-0";
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
        $VoiceId = $object->MediaId;
        $resultStr = $this->responseVoice($object, $VoiceId);
        return $resultStr;
    }
    
    public function responseText($object, $content, $flag=0)
    {
        $resultStr = sprintf($GLOBALS["textTpl"], $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        return $resultStr;
    }

    public function responseVoice($object, $voiceId, $flag=0)
    {
        $resultStr = sprintf($GLOBALS["voiceTpl"], $object->FromUserName, $object->ToUserName, time(), $voiceId, $flag);
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