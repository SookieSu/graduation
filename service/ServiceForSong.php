<?php
$dir = dirname(__FILE__);
require_once($dir.'/../consts/WxConfig.php');
require_once($dir.'/../consts/MsgType.php');
require_once($dir.'/../DB/DBMocks.php');
require_once($dir.'/../api/mpApi.php');
//header("content-type:text/html;charset=utf-8");
ServiceForSong::start();

class ServiceForSong
{
	

	public static function start()
	{
		$method = $_GET['method'];
		$songName = $_POST['song_name'];
		$songUrl = $_POST['song_url'];
		$songID = $_POST['song_id'];
		
    	if($method == 'querySong')
    	{
    		$userID = self::getUserID();
    		self::querySong($userID);
    	}
    	if($songName != null && $songUrl )
    	{
    		$userID = self::getUserID();
    		self::addSong($userID,$songName,$songUrl);
    	}
    	if($songID != null)
    	{
    		$userID = self::getUserID();
    		self::deleteSong($userID,$songID);
    	}
	}
	public static function getUserID()
	{
		$refer = $_SERVER['HTTP_REFERER'];
		$retData = DBMocks::querySNSAccessToken();
		$retSNSAccessToken = json_decode($retData['data'],true);
		preg_match('/code=[a-zA-Z0-9-]+/', $refer,$retCode);
    	$tmpcode = explode("=", $retCode[0]);
    	$code = $tmpcode[1];
    	//echo var_dump($retData);
    	//echo $code;
    	if($code == $retData['code'])
    	{
    		//从以前的code中取openid
    		$userID = $retSNSAccessToken['openid'];
    	}
    	else
    	{
    		//更新code
    		$newSNSAccessToken = mpApi::getSNSAccessToken($code);
    		$userID = $newSNSAccessToken['openid'];
    		DBMocks::updateSNSAccessToken($code,$newSNSAccessToken);
    	}
    	//echo "getUserID : \n".$userID;
    	return $userID;
	}
	public static function addSong($userID,$songName,$songUrl)
	{
		echo "print in addSong ! \n";
		$myfilename = "song-".$songName."-".time().".mp3";
		$retData = array('songName' => $songName, 'songUrl' => $songUrl );
		$data = json_encode($retData);
		if (DBMocks::addMediaInfo(MsgType::MEDIADATA,$userID,MsgType::SONG,$data) == true){
			return DBMocks::addMessageInfo(MsgType::DEVICEDATA,$userID,MsgType::SONG_ADD,$data);
		}else{
			echo "add Song failed ! \n";
			return false;
		}
	}
	public static function querySong($userID)
	{
		$retData = DBMocks::queryMediaInfo(MsgType::MEDIADATA,$userID,MsgType::SONG);
		echo var_dump($retData);
		return json_encode($retData);
	}
	public static function deleteSong($userID,$songID)
	{
		echo "print in deleteSong ! \n";
		if (DBMocks::queryMediaInfo(MsgType::MEDIADATA,$userID,MsgType::SONG,$songID) != false)
		{
			DBMocks::deleteMessageInfo(MsgType::MEDIADATA,$songID);
			return DBMocks::addMessageInfo(MsgType::DEVICEDATA,$userID,MsgType::SONG_DELETE,null);
		}
		else{
			echo "song doesn't exist ! \n";
			return false;
		}
		
	}
}
?>
