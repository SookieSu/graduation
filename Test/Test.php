<?php

$dir = dirname(__FILE__);
require_once($dir.'/../tools/AccessToken.php');
require_once($dir.'/../api/mpApi.php');
require_once($dir.'/../DB/DBMocks.php');

DBMocks::Test();

?>