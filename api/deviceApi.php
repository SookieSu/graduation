<?php
/*
 * @className : deviceApi
 * @classDescription : 通过设备ID获取最新状态：
 * 1、语音通信模式：是否有新消息；
 * 2、儿歌获取模式：是否有增加/删除 儿歌/故事的状态；
 * 用于设备获取服务器更新api，处理设备get/post请求
*/
$dir = dirname(__FILE__);
require_once($dir.'/../consts/WxConfig.php');
require_once($dir.'/../consts/MsgType.php');
require_once($dir.'/../DB/DBMocks.php');
use sinacloud\sae\Storage as Storage;
Storage::setAuth(WxConfig::AccessKey, WxConfig::SecretKey);
//echo "hello world!";
deviceApi::start();

class deviceApi{
	//private static $_mysql = new DBMocks();
	private static $_result = array(
		array(
		'status' => MsgType::UPDATED,
		'data' => array()
		)
	);
	private static $_out = array();

	public static function start()
	{
		$deviceID = $_GET['deviceID'];
		$methodGet = $_GET['method'];
    $methodPost = $_POST['method'];
    //echo var_dump($_POST);
    switch ($methodGet)
    {
      case 'getData':
        if($deviceID != null)
        {  
          $resultStr = self::getData($deviceID);
          echo utf8_encode($resultStr);
          //self::deleteIsReadMessage($deviceID);
        }
        break;
      default:
        //echo "Unknown method ! \n";
        break;
    }
    switch ($methodPost)
    {
      case 'postData':
        //echo "postData from device\n";
        $deviceID = $_POST['deviceID'];
        $data = $_POST['data'];
        //var_dump($deviceID);
        //var_dump($data);
        $resultStr = self::postData($deviceID,$data);
        echo $resultStr;
        break;
      default:
        //echo "Unknown method ! \n";
        break;
    }
	}

	/*
	 * @funcName : getData
 	 * @funcDescription : 通过设备ID获取最新状态：
	 * 1、语音通信模式：是否有新消息；
	 * 2、儿歌获取模式：是否有增加/删除 儿歌/故事的状态；
	 * @funcParam : deviceID
	*/
	public static function getData($deviceID)
	{
  		//echo "getData!";
      //获取devicedata中的未读信息
  		$_result = DBMocks::queryMessageInfo(Msgtype::DEVICEDATA,$deviceID);//test
      //全设已读
      /*
      if($_result != null)
      {
        //echo 'not null retData !';
        foreach($_result as $record) 
        {
          DBMocks::setMessageReadInfo(MsgType::DEVICEDATA,$record['id']);
        }
      }
      */
      /*//暂时不需要这一段0-0，从数据库中把设备的未读信息都取出来，直接返回就好了。
  		for($index = 0;$index < count($_result);$index++) {
  			# code...
  			switch ($_result[$index]['msgtype']) {
  				case MsgType::VOICE:
  					# code...
  					echo "VOICE!";
  					$_result[$index]['data'] = $this->getLatestVoice($deviceID);
            //$this->deleteIsReadMessage($deviceID);
  					break;
  				case MsgType::SONG_ADD:
  					# code...
  					echo "SONG_ADD!";
  					$_result[$index]['data'] = $this->getSong($deviceID);
  					break;
          case MsgType::SONG_DELETE:
  					# code...
  					echo "SONG_DELETE!";
  					break;
  				case MsgType::STORY_ADD:
  					# code...
  					echo "STORY_ADD!";
  					$_result[$index]['data'] = $this->getStory($deviceID);
  					break;
  				case MsgType::STORY_DELETE:
  					# code...
  					echo "STORY_DELETE!";
  					break;
  				default:
  					# code...
  					echo "Unknown Status!";
  					break;
  			}	
  		}
      */
      /*
  		foreach ($_result as $value) {
        echo 'msgtype:'.$value['msgtype'];
  			echo 'data:'.$value['data'];
  		}
      */
  		if($_result){
  			$_out['message'] = Msgtype::SUCCESS;
  			$_out['code'] = Msgtype::SUCCESSCODE;
  			$_out['data'] = $_result;
  			//echo json_encode($_out);
  		}
  		else{
  			$_out['message'] = MsgType::FAILED;
  			$_out['code'] = 0;
  			$_out['data'] = null;
  			//echo json_encode($_out);
  		}
  		return json_encode($_out);
  }


  public static function postData($deviceID,$data)
  {
    $myfilename = "voice-".time().".amr";
    $bucketName = MsgType::VOICEFROMDEVICE;
    $bucketInfo = Storage::getBucketInfo($bucketName);
    $s = new SaeStorage();
    //test addVoice
    if($data != null)
    {
      //把retData写入bucket中，文件取名为myfilename
      $s->write ( $bucketName ,  $myfilename , $data );
      //获取存入storage后的url
      $retUrl = $s->getUrl($bucketName,$myfilename);
      echo $retUrl;
    } 
    $retbound = DBMocks::queryBoundInfo($deviceID);
    //echo "print retbound ! ".var_dump($retbound);
    if($retbound == null)
    {
      return false;
    }
    foreach ($retbound as $record)
    {
      $userID = $record['userID'];
      $deviceID = $record['deviceID'];
      $retFlag = DBMocks::addMessageInfo(MsgType::WEIXINDATA,$userID,MsgType::VOICE,$retUrl);
      if ($retFlag != true)
      {
        error_log("postData failed!");
        return false;
      }
    }
    return true;
  }
	/*
	 * @funcName : getLatestVoice
	 * @funcDescription : 通过设备ID获取对应微信ID，查询数据库是否有更新的语音消息，返回语音消息
	 * @funcParam : deviceID
	*/
	protected function getLatestVoice($deviceID)
	{
    //sae_xhprof_start();//debug start

		//echo "getLatestVoice!";
    $retData = DBMocks::queryMessageInfo(MsgType::DEVICEDATA,$deviceID,false);
    if($retData != null)
    {
      //echo 'not null retData !';

      foreach($retData as $record) 
      {
        DBMocks::setMessageReadInfo(MsgType::DEVICEDATA,$record['id']);
        //sae_xhprof_end();//debug end
      }
    }
    else
    {
      return MsgType::UPDATED;
    }
		return $retData;
	}

  protected function deleteIsReadMessage($deviceID)
  {
    echo "deleteIsReadMessage!";
    $retData = DBMocks::queryMessageInfo(MsgType::DEVICEDATA,$deviceID,true);
    if($retData != null)
    {
      echo var_dump($retData);
      foreach($retData as $record) 
      {
        DBMocks::deleteMessageInfo(MsgType::DEVICEDATA,$record['id']);
      }
    }
    else
    {
      return MsgType::UPDATED;
    }
    return true;
  }
}
?>