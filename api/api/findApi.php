<?php
$dir = dirname(__FILE__);
require_once($dir.'/../consts/WxConfig.php');
require_once($dir.'/../consts/MsgType.php');
require_once($dir.'/../DB/DBMocks.php');
require_once($dir.'/../tools/HttpUtil.php');
require_once($dir.'/../tools/BaiduMusic.php');

findApi::start();

class findApi{

	const BaiduMusicUrl = "http://music.baidu.com";
	const SearchSongUrl = "http://music.baidu.com/search?key=";
	const SearchStoryUrl = "http://music.baidu.com/search?key=";

	public static function start()
	{
		$method = $_GET['method'];
    	$songName = $_GET['song'];
    	switch ($method)
        {
        	case "findSong":
        		if($songName != null)
        		{
  					self::findSong($songName);
  				}
  				break;
  			default :
  				//echo "unknown method !\n";
  				break;
  		}	
	}

	public static function findSong($songName)
	{
		echo "print in findSong\n";
		$detailUrl = self::getDetailUrl($songName);
		$retSongArray = BaiduMusic::getSong($songName);
		//此处返回数组第一个元素
		$retSong = $retSongArray[0];
		$retSong['playUrl'] = $detailUrl;
		var_dump($retSong);
		return $retSong;
	}

	private static function getDetailUrl($songName)
	{
		$url = self::SearchSongUrl.$songName;
		echo $url;
		$retData = HttpUtil::executeGet($url);
		preg_match('/song-title.*href.*>/', $retData,$songItemArray);
		preg_match('/href="[\/\w]+"/', $songItemArray[0],$detailUrlArray);
		//echo var_dump($songItemArray);
		//echo var_dump($detailUrlArray);
		$tmpdetailUrl = explode("=", $detailUrlArray[0]);
		$detailUrl = substr($tmpdetailUrl[1], 1, -1);
		echo $detailUrl;
		return self::BaiduMusicUrl.$detailUrl;
	}

	public static function findStory($storyName)
	{
		return ;
	}
}
?>