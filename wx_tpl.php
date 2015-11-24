<?php

$GLOBALS['textTpl'] = 
            "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0</FuncFlag>
            </xml>";   
$GLOBALS["newsTpl"] = 
          "<xml>
           <ToUserName><![CDATA[%s]]></ToUserName>
           <FromUserName><![CDATA[%s]]></FromUserName>
           <CreateTime>%s</CreateTime>
           <MsgType><![CDATA[news]]></MsgType>
           <ArticleCount>%s</ArticleCount>
           <Articles>
           <item>
           <Title><![CDATA[%s]]></Title> 
           <Description><![CDATA[%s]]></Description>
           <PicUrl><![CDATA[%s]]></PicUrl>
           <Url><![CDATA[%s]]></Url>
           </item>
           </Articles>
           <FuncFlag>1</FuncFlag>
           </xml> ";
$GLOBALS["musicTpl"] =
            "<xml>
             <ToUserName><![CDATA[%s]]></ToUserName>
             <FromUserName><![CDATA[%s]]></FromUserName>
             <CreateTime>%s</CreateTime>
             <MsgType><![CDATA[music]]></MsgType>
             <Music>
             <Title><![CDATA[%s]]></Title>
             <Description><![CDATA[%s]]></Description>
             <MusicUrl><![CDATA[%s]]></MusicUrl>
             <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
             </Music>
             <FuncFlag>0</FuncFlag>
             </xml>";
$GLOBALS["voiceTpl"] = 
            "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[voice]]></MsgType>
            <Voice>
            <MediaId><![CDATA[%s]]></MediaId>
            </Voice>
            <FuncFlag>%d</FuncFlag>
            </xml>";
$GLOBALS["device_textTpl"] =
            "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%u</CreateTime>
            <MsgType><![CDATA[device_text]]></MsgType>
            <DeviceType><![CDATA[%s]]></DeviceType>
            <DeviceID><![CDATA[%s]]></DeviceID>
            <SessionID>%u</SessionID>
            <Content><![CDATA[%s]]></Content>
            </xml>";
$GLOBALS["device_eventTpl"] =
            "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%u</CreateTime>
            <MsgType><![CDATA[device_event]]></MsgType>
            <Event><![CDATA[%s]]></Event>
            <DeviceType><![CDATA[%s]]></DeviceType>
            <DeviceID><![CDATA[%s]]></DeviceID>
            <Content><![CDATA[%s]]></Content>
            <SessionID>%u</SessionID>
            <OpenID><![CDATA[%s]]></OpenID>
            </xml>";
?>