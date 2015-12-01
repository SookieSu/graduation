<?php
/*
 * @className : mpApi
 * @classDescription : 微信客户端调用功能函数：
 * 1、通过扫码 -- 绑定/解绑微信id和设备id
 * 2、通过菜单 -- 增加/删除故事/儿歌
 * 用于处理微信公众号菜单服务以及绑定设备服务
*/
require_once($dir.'/../DB/DBMocks.php');

$mpApiObj = new mpApi();
$mpApiObj->addBound('20151130','2');
$mpApiObj->addBound('20151201','1');
$mpApiObj->addBound('20151202','2');
$mpApiObj->removeBound('20151230','2');

class mpApi
{
	const GetAccessTokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid="
			+ WxConfig::APPID + "&secret=" + WxConfig::APPSECRET;
	const CustomSendUrl = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN";
	const CreateMenuUrl = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN";
	const QueryMenuUrl = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=ACCESS_TOKEN";
	const DeleteMenuUrl = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=ACCESS_TOKEN";
	/*
	 * @funcName : addBound
 	 * @funcDescription : 微信用户通过扫码绑定deviceID
	 * @funcParam : userID , deviceID
	*/
	public function addBound($userID,$deviceID)
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

	public function removeBound($userID,$deviceID)
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
}

?>