<?php
/*
 * @className : deviceApi
 * @classDescription : 通过设备ID获取最新状态：
 * 1、语音通信模式：是否有新消息；
 * 2、儿歌获取模式：是否有增加/删除 儿歌/故事的状态；
 * 用于设备获取服务器更新api，处理设备get/post请求
*/
$dir = dirname(__FILE__);
require_once($dir.'/../consts/MsgType.php');
require_once($dir.'/../DB/DBMocks.php');

echo "hello world!";


$deviceApiObj = new deviceApi();
$deviceApiObj->start();

class deviceApi{
	//private static $_mysql = new DBMocks();
	private static $_result = array(
		array(
		'status' => MsgType::UPDATED,
		'data' => array()
		)
	);
	private static $_out = array();

	public function start()
	{
		$deviceID = $_GET['deviceID'];
		$method = $_GET['method'];
    
		if($deviceID != '' && $method == 'getStatus'){
  			$this->getStatus($deviceID);
  		}
	}

	/*
	 * @funcName : getStatus
 	 * @funcDescription : 通过设备ID获取最新状态：
	 * 1、语音通信模式：是否有新消息；
	 * 2、儿歌获取模式：是否有增加/删除 儿歌/故事的状态；
	 * @funcParam : deviceID
	*/
	protected function getStatus($deviceID)
	{
  		echo "getStatus!";
  		$_result = DBMocks::queryStatus($deviceID);
  		for($index = 0;$index < count($_result);$index++) {
  			# code...
  			switch ($_result[$index]['status']) {
  				case MsgType::UPDATED:
  					# code...
  					echo "UPDATED!";
  					break;
  				case MsgType::MSG_UNREAD :
  					# code...
  					echo "MSG_UNREAD!";
  					$_result[$index]['data'] = $this->getLatestVoice($deviceID);
  					break;
  				case MsgType::SONG_ADD:
  					# code...
  					echo "SONG_ADD!";
  					$_result[$index]['data'] = $this->getSong($deviceID);
  					break;
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
  		foreach ($_result as $value) {
  			echo 'status:'.$value['status'];
  			echo 'data:'.$value['data'];
  		}
  		if($_result){
  			$_out['message'] = SUCCESS;
  			$_out['code'] = SUCCESSCODE;
  			$_out['data'] = $_result;
  			echo json_encode($_out);
  		}
  		else{
  			$_out['message'] = FAILED;
  			$_out['code'] = 0;
  			$_out['data'] = null;
  			echo json_encode($_out);
  		}
  		return $_result;
  	}

	/*
	 * @funcName : getLatestVoice
	 * @funcDescription : 通过设备ID获取对应微信ID，查询数据库是否有更新的语音消息，返回语音消息
	 * @funcParam : deviceID
	*/
	protected function getLatestVoice($deviceID)
	{
		echo "getLatestVoice!";
		return 1;
	}

	protected function getSong($deviceID)
	{
  		echo "getSong!";
  		return 2;
	}

	protected function getStory($deviceID)
	{
  		echo "getStory!";
  		return 3;
	}


}
?>