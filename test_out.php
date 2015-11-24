<?php
echo "hello world!";
$deviceID = $_GET['deviceID'];
$method = $_GET['method'];

if($deviceID != '' && $method == 'getLatestVoice'){
  getLatestVoice($deviceID);
}


function getLatestVoice($deviceID)
{
  echo "hello world2!";
  return 0;
}

?>