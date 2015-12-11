<?php
define("SUCCESS",true);
define("FAILED",false);
define("SUCCESSCODE",200);

class MsgType{


  //Status define，绑定状态
  const BIND = 1;
  const UNBIND = 1;

  //设备获取以及微信获取的状态
  const UPDATED = 2;
  const MSG_UNREAD = 3;

  //获取的信息类型
  const VOICE = 4;

  //设备获取特有
  const SONG_ADD = 5;
  const SONG_DELETE = 6;
  const STORY_ADD = 7;
  const STORY_DELETE = 8;

  //数据表名称定义
  const ACCESSTOKEN = "AccessToken";//存放AccessToken
  const DEVICEDATA = "DeviceData";//供给设备拉数据的数据库，存放微信->设备的指令。
  const WEIXINDATA = "WeixinData";//存放设备->微信的语音
  const BOUNDDATA = "BoundData";//存放微信与设备绑定的信息

  //微信消息模板
  const textTpl = 
            "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0</FuncFlag>
            </xml>";   
  const newsTpl = 
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
  const musicTpl =
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
  const voiceTpl = 
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
  const device_textTpl =
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
   const device_eventTpl =
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

}

?>