<?php
header("content-type:text/html;charset=utf-8");
ServiceForSong::start();

class ServiceForSong
{
	public static function start()
	{
		$songName = $_POST['song_name'];
		$songUrl = $_POST['song_url'];
		$songID = $_POST['song_id'];
    	echo "ServiceForSong Started ! \n";
	}
}
?>
