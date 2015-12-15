<?php
$dir = dirname(__FILE__);
require_once($dir.'/../consts/WxConfig.php');
require_once($dir.'/../consts/MsgType.php');
require_once($dir.'/../DB/DBMocks.php');

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
    	echo "ServiceForSong Started ! \n";
    	//self::querySong($userID);
    	if($method == 'querySong')
    	{
    		self::querySong($userID);
    	}
    	if($songName != null && $songUrl )
    	{
    		self::addSong($userID,$songName,$songUrl);
    	}
    	if($songID != null)
    	{
    		self::deleteSong($userID,$songID);
    	}
	}
	public static function addSong($userID,$songName,$songUrl)
	{
		echo "print in addSong ! \n";
		$myfilename = "song-".$songName."-".time().".mp3";
		$retData = array('songName' => $songName, 'songUrl' => $songUrl );
		if (DBMocks::addMediaInfo(MsgType::MEDIADATA,$userID,MsgType::SONG,$retData) == true){
			return DBMocks::addMessageInfo(MsgType::DEVICEDATA,$userID,MsgType::SONG_ADD,$retData);
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
