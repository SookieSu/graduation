<?php
/*
 * @className : DBMocks
 * @classDescription : 
 * 1、增加/删除/查询 微信与设备绑定信息
 * 2、增加/获取 微信给设备发的语音
 * 3、增加/获取 微信给设备发的指令：
 *		a、增加/删除 儿歌
 *		b、增加/删除 故事
 * 用于请求数据库获取数据
*/
$dir = dirname(__FILE__);
require_once($dir.'/../consts/MsgType.php');

//数据表名称定义
define("ACCESSTOKEN","AccessToken");//存放AccessToken
define("DEVICEDATA","DeviceData");//供给设备拉数据的数据库，存放微信->设备的指令。
define("WEIXINDATA","WeixinData");//存放设备->微信的语音
define("BOUNDDATA","BoundData");//存放微信与设备绑定的信息

$mysql_instance = DBMocks::getInstance();

class DBMocks{

	//定义单例
	private static $_instance;
	//定义数据库变量
	private static $mysql;
	//保存设备绑定信息
	private static $_bound = array();
	//保存设备获取的状态信息
	private static $_status = array();

	//创建__clone方法防止对象被复制克隆
	public function __clone(){
		trigger_error('Clone is not allow!',E_USER_ERROR);
	}
 
	//单例方法,用于访问实例的公共的静态方法
	public static function getInstance()
	{
		if(!(self::$_instance instanceof self))
		{
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	//构造函数
	private function __construct()
	{
		self::$mysql = new SaeMysql();

		//创建DeviceData数据表
		/*
		$sql = "CREATE TABLE WeixinData (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		userid  VARCHAR(30) NOT NULL,
		deviceid VARCHAR(30) NOT NULL,
		msgtype VARCHAR(30) NOT NULL,
		isread VARCHAR(30) NOT NULL,
		data MEDIUMBLOB,
		reg_date TIMESTAMP
		)";
		*/
		//$mysql->closeDb();
	}

	public static function queryBoundInfo($id)
	{
		//suppose userID
		$sql = "SELECT `userID`,`deviceID` FROM " . BOUNDDATA . " WHERE userID = '$id'" ;
		$data = self::$mysql->getData( $sql );
		if( self::$mysql->errno() != 0 )
		{
    		die( "Error in queryBoundInfo :" . self::$mysql->errmsg() );
		}
		else if($data == null)
		{
			//suppose deviceID
			$sql = "SELECT `userID`,`deviceID` FROM " . BOUNDDATA . " WHERE deviceID = '$id'" ;
			$data = self::$mysql->getData( $sql );
		}
		//此时拿到了含有id的多维数组data
		$_bound = $data;
		echo json_encode($_bound);
		return $_bound;
	}

	public static function queryStatus($deviceID)
	{

		$sql = "SELECT * FROM ".DEVICEDATA. " LIMIT 10";
		$data = self::$mysql->getLine( $sql );
		self::$mysql->runSql( $sql );
		if( self::$mysql->errno() != 0 )
		{
    		die( "Error in queryStatus :" . self::$mysql->errmsg() );
		}
		else{
			//echo json_encode($data);
			$_status = array($data);
		}

		/*
		$_status = array(
			array(
				'status' => MsgType::UPDATED,
				'data' => null
				),
			array(
				'status' => MsgType::MSG_UNREAD,
				'data' => null
				)
			);

		*/

		return $_status;
	}
	
	public static function changeStatus($deviceID)
	{

	}

	public static function queryDeviceData($deviceID)
	{
		$sql = "SELECT * FROM ".DEVICEDATA. " WHERE isread = 'false' AND deviceid = "."'$deviceID'";//
		$data = self::$mysql->getData( $sql );
		if( self::$mysql->errno() != 0 && $data == null )
		{
    		die( "Error in queryDeviceData :" . self::$mysql->errmsg() );
		}
		else {
			//echo json_encode($data);
			echo var_dump($data);
			return $data;
		}
	}
	public static function addVoiceInfo($userID,$voice)
	{
		$retbound = self::queryBoundInfo($userID);
		foreach ($retbound as $record) 
		{
			# code...
			$deviceID = $record['deviceID'];
			$msgtype = MsgType::MSG_UNREAD;
			$isread = 'false';
			echo $deviceID.$msgtype.$isread;
			$sql = "INSERT INTO ".DEVICEDATA."( `userid` ,`deviceid` , `msgtype` , `isread` , `data` ) "." VALUES "." ( '$userID' , '$deviceID' , '$msgtype' , '$isread' , '$voice' ) ";
			echo "sql : ". $sql;
			self::$mysql->runSql( $sql );
			if( self::$mysql->errno() != 0 )
			{
			    die( "Error in addVoiceInfo :" . self::$mysql->errmsg() );
			    return false;
			}
			else
			{
				echo "success add voice  : " . $userID . " : " . $deviceID . " ! \n";
				return true;
			}

		}
	}
	public static function saveBoundInfo($userID,$deviceID)
	{
		$sql = "INSERT  INTO ".BOUNDDATA." ( `userid` , `deviceid` ) "." VALUES "." ( '$userID' , '$deviceID') ";
		self::$mysql->runSql( $sql );
		if( self::$mysql->errno() != 0 )
		{
    		die( "Error in saveBoundInfo :" . self::$mysql->errmsg() );
		}
		else
		{
			echo "success bound  : " . $userID . " : " . $deviceID . " ! \n";
			return true;
		}

	}

	public static function removeBoundInfo($userID,$deviceID)
	{
		$sql = "DELETE FROM ".BOUNDDATA." WHERE userID = '$userID' AND deviceID = '$deviceID'";
		self::$mysql->runSql( $sql );
		if( self::$mysql->errno() != 0 )
		{
    		die( "Error in removeBoundInfo :" . self::$mysql->errmsg() );
		}
		else
		{
			echo "success delete bound info  : " . $userID . " : " . $deviceID . " ! \n";
			return true;
		}
	}

	public static function updateAccessToken($access_token)
	{
		$sql = "UPDATE ".ACCESSTOKEN." SET access_token = "."'$access_token'"." WHERE id = '1' ";
		//echo $sql;
		self::$mysql->runSql( $sql );
		if( self::$mysql->errno() != 0 )
		{
    		die( "Error in updateAccessToken :" . self::$mysql->errmsg() );
		}
		else
		{
			echo "success update access_token  : " . $access_token ." ! \n";
			return true;
		}
	}
	public static function queryAccessToken()
	{
		$sql = "SELECT * FROM ".ACCESSTOKEN. " WHERE id = '1' ";
		$data = self::$mysql->getLine( $sql );
		
		if( self::$mysql->errno() != 0 && $data == null )
		{
    		die( "Error in queryAccessToken :" . self::$mysql->errmsg() );
		}
		else if (array_key_exists('access_token',$data)){
			//echo json_encode($data);
			//echo var_dump($data);
			return $data['access_token'];
		}
	}
}


?>
