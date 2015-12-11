<?php
/*
 * @className : mpApi
 * @classDescription : 微信客户端调用功能函数：
 * 1、通过扫码 -- 绑定/解绑微信id和设备id
 * 2、通过菜单 -- 增加/删除故事/儿歌
 * 用于处理微信公众号菜单服务以及绑定设备服务
*/
$dir = dirname(__FILE__);
require_once($dir.'/../consts/WxConfig.php');
require_once($dir.'/../consts/MsgType.php');
require_once($dir.'/../DB/DBMocks.php');
require_once($dir.'/../tools/HttpUtil.php');

$mpApiObj = new mpApi();
$mpApiObj->start();
//$mpApiObj->addBound('20151130','2');
//$mpApiObj->addBound('20151201','1');
//$mpApiObj->addBound('20151202','2');
//$mpApiObj->removeBound('20151230','2');
//echo mpApi::getAccessToken();

class mpApi
{
	const GetAccessTokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WxConfig::APPID."&secret=".WxConfig::APPSECRET;
	const CustomSendUrl = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN";
	const CreateMenuUrl = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN";
	const QueryMenuUrl = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=ACCESS_TOKEN";
	const DeleteMenuUrl = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=ACCESS_TOKEN";
	const GetMediaUrl = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID";

	public function start()
	{
		$userID = $_GET['userID'];
		$method = $_GET['method'];
    
		if($userID != '' && $method == 'queryBound'){
  			$this->queryBound($userID);
  		}
	}

	/**
		 * 获取访问凭证
		 * <p>
		 * 正常情况下access_token有效期为7200秒，重复获取将导致上次获取的access_token失效。
		 * 由于获取access_token的api调用次数非常有限，需要全局存储与更新access_token
	*/
	public static function getAccessToken()
	{
		$resultAccessToken = HttpUtil::executeGet(self::GetAccessTokenUrl);
		$jsonAccessToken = json_decode($resultAccessToken,true);
		return $jsonAccessToken;
		//此处返回获取access_token接口后的json对象
	}

	public static function queryBound($id)
	{
		$retarr = DBMocks::queryBoundInfo($id);
		echo var_dump($retarr);
		return $retarr;
	}
	/*
	 * @funcName : addBound
 	 * @funcDescription : 微信用户通过扫码绑定deviceID
	 * @funcParam : userID , deviceID
	*/
	public static function addBound($userID,$deviceID)
	{
		//要先判定是不是在db中已经存在该记录，不存在则绑定，存在则提示已存在
		$retarr = DBMocks::queryBoundInfo($deviceID);
		//echo json_encode($retarr);
		if($retarr == null)
		{
			//说明之前没有该id的记录,绑定
			return DBMocks::saveBoundInfo($userID,$deviceID);
		}
		else
		{
			 foreach ($retarr as $value) {
			 	# code...
			 	if($value['userID'] == $userID)
			 	{
			 		//说明之前有该id的记录,返回
			 		echo "this device and wechat has bound before : ".$userID." : ".$deviceID." ! \n";
			 		return false;
			 	}
			 }
			 //说明之前没有该id的记录,绑定
			 return DBMocks::saveBoundInfo($userID,$deviceID);
		}
	}

	public static function removeBound($userID,$deviceID)
	{
		//先判断db中是否有该记录，有则删除，没有则返回记录不存在
		$retarr = DBMocks::queryBoundInfo($deviceID);
		//echo json_encode($retarr);
		if($retarr == null)
		{
			//说明之前没有该id的记录,解绑失败
			echo "this device and wechat has never bound before : ".$userID." : ".$deviceID." ! \n";
			return false;
		}
		else
		{
			 foreach ($retarr as $value) {
			 	# code...
			 	if($value['userID'] == $userID)
			 	{
			 		//说明之前有该id的记录,返回
			 		return DBMocks::removeBoundInfo($userID,$deviceID);
			 	}
			 }
			 //说明之前没有该id的记录,绑定
			 echo "this device and wechat has never bound before : ".$userID." : ".$deviceID." ! \n";
			 return false;
		}
	}

	public static function addVoice($userID,$mediaID)
	{
		$realurl = str_replace("MEDIA_ID",$mediaID,self::GetMediaUrl);
		//echo $realurl;
		$retData = HttpUtil::doGet($realurl);
		echo "print in addVoice ! \n";
		//echo var_dump($retData);
		//test addVoice
		//if($retData != false)
		//{
		DBMocks::addMessageInfo(MsgType::DEVICEDATA,'20151130',MsgType::VOICE,$retData);
		//}
		//return $retData;
	}

	/**
	 * 创建自定义菜单<p>
	 * 文档位置：自定义菜单->自定义菜单创建接口
	 */
	public static function menuCreate() {
		$menuPostString = '{
		 "button":[
		 {"name":"儿歌",
		 "sub_button":[
		 {
		 "type":"click",
		 "name":"添加儿歌",
		 "key":"1100"
		 },
		 {
		 "type":"click",
		 "name":"查询儿歌",
		 "key":"2200"
		 },
		 {
		 "type":"click",
		 "name":"删除儿歌",
		 "key":"2200"
		 }]},
		 {
		 "name":"故事",
		 "sub_button":[
		 {
		 "type":"click",
		 "name":"添加故事",
		 "key":"1101"
		 },
		 {
		 "type":"click",
		 "name":"查询故事",
		 "key":"2001"
		 },
		 {
		 "type":"click",
		 "name":"删除故事",
		 "key":"2001"
		 }]}
		 ]}]
		 }';
		return HttpUtil::doPost(self::CreateMenuUrl, $menuPostString);
	}

	/**
	 * 查询自定义菜单<p>
	 * 文档位置：自定义菜单->自定义菜单查询接口
	 */
	public static function menuQuery() 
	{
		return HttpUtil::doGet(self::QueryMenuUrl);
	}
	
	/**
	 * 删除自定义菜单<p>
	 * 文档位置：自定义菜单->自定义菜单删除接口
	 */
	public static function menuDelete() 
	{
		return HttpUtil::doGet(self::DeleteMenuUrl);
	}
}

?>