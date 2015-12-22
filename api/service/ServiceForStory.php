<?php
//header("content-type:text/html;charset=utf-8");
ServiceForStory::start();

class ServiceForStory
{
	public static function start()
	{
		$storyName = $_POST['story_name'];
		$storyUrl = $_POST['story_url'];
		$storyID = $_POST['story_id'];
    	//echo "ServiceForStory Started ! \n";
	}
}
?>