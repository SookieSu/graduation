<?php

$dir = dirname(__FILE__);
require_once($dir.'/../tools/AccessToken.php');
require_once($dir.'/../api/mpApi.php');
require_once($dir.'/../DB/DBMocks.php');
require_once($dir.'/../tools/HttpUtil.php');

$method = $_GET['method'];
$id = $_GET['userID'];
$data = DBMocks::Test($method,$id);
var_dump($data);
//Test::testforpost();
//echo AccessTokenUtil::getTokenStr();
class Test{

	public static function testforpost()
	{
		$postArray = array(
			"method" => "postData",
			"deviceID" => 0,
			"data" => "test".time()
			);
		$url = "http://2.sookiesu.sinaapp.com/webserver/CallBackServlet.php";
		$retData = HttpUtil::executePost($url,$postArray);
		var_dump($retData);
	}
	

	
}

?>