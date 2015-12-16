<?php


class MsgType{

  /*  wechat状态  */
  //Status define，绑定状态
  const BIND = "bind";
  const UNBIND = "unbind";

  // ---------基础消息
  // 接收/响应
  const TEXT = "text";
  const IMAGE = "image";
  const VOICE = "voice";
  const VIDEO = "video";

  // 接收
  const LOCATION = "location";
  const LINK = "link";

  // 响应
  const MUSIC = "music";
  const NEWS = "news";

  // ---------事件推送
  const EVENT = "event";

  // 事件具体类型
  const SUBSCRIBE = "subscribe";// 订阅
  const UNSUBSCRIBE = "unsubscribe";// 取消订阅
  const SCAN = "SCAN";// 扫码
  const CLICK = "CLICK";// 点击菜单拉取消息
  const VIEW = "VIEW";// 点击菜单跳转链接

  //网页授权获取用户基本信息接口
  //SCOPE
  const SNSAPI_BASE = "snsapi_base";
  const SNSAPI_USERINFO = "snsapi_userinfo";

  /*  device状态  */

  //设备获取以及微信获取的状态
  const UPDATED = "updated";
  const MSG_UNREAD = "msg_unread";

  //获取的信息类型
  //const VOICE = "voice";

  //设备获取特有
  const SONG_ADD = "song_add";
  const SONG_DELETE = "song_delete";
  const STORY_ADD = "story_add";
  const STORY_DELETE = "story_delete";

  const SUCCESS = true;
  const FAILED = false;
  const SUCCESSCODE = 200;
  
  //storage domain定义
  const VOICEFROMWECHAT = "voicefromwechat";
  const VOICEFROMDEVICE = "voicefromdevice";
  const SONG = "song";
  const STORY = "story";

  //数据表名称定义
  const ACCESSTOKEN = "AccessToken";//存放AccessToken
  const SNSACCESSTOKEN = "SNSAccessToken";//存放code与openid
  const DEVICEDATA = "DeviceData";//供给设备拉数据的数据库，存放微信->设备的指令。
  const WEIXINDATA = "WeixinData";//存放设备->微信的语音
  const BOUNDDATA = "BoundData";//存放微信与设备绑定的信息
  const MEDIADATA = "MediaData";//存放多媒体文件信息

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