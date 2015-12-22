<?php

$dir = dirname(__FILE__);
require_once($dir.'/../tools/AccessToken.php');
require_once($dir.'/../api/mpApi.php');
require_once($dir.'/../DB/DBMocks.php');


$method = $_GET['method'];
$id = $_GET['userID'];
<<<<<<< HEAD
<<<<<<< HEAD
DBMocks::Test($method,$id);
=======
//DBMocks::Test($method,$id);
//Test::testforpost();
echo AccessTokenUtil::getTokenStr();
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
>>>>>>> 7407106434291e8918529c593a782d5b0351ab9e
=======
DBMocks::Test($method,$id);
>>>>>>> parent of 0e2fc6c... getVoice from device complete

?>