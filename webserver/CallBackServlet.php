<?php
use sinacloud\sae\Storage as Storage;

$dir = dirname(__FILE__);
require_once($dir.'/../consts/WxConfig.php');
require_once($dir.'/../consts/MsgType.php');
require_once($dir.'/../DB/DBMocks.php');
require_once($dir.'/../tools/AccessToken.php');
require_once($dir.'/../tools/AccessTokenUtil.php');
require_once($dir.'/../tools/HttpUtil.php');
require_once($dir.'/../tools/BaiduMusic.php');
require_once($dir.'/../api/Message.php');

require_once($dir.'/../api/deviceApi.php');
require_once($dir.'/../api/findApi.php');
require_once($dir.'/../api/mpApi.php');
require_once($dir.'/../service/ServiceForSong.php');
require_once($dir.'/../service/ServiceForStory.php');
require_once($dir.'/../service/CallBackService.php');
?>
