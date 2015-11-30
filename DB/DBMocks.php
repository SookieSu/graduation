<?php
$dir = dirname(__FILE__);
require_once($dir.'/../consts/MsgType.php');


define("DEVICEDATA","DeviceData");
define("WEIXINDATA","WeixinData");
define("BOUNDDATA","BoundData");

$mysql_instance = DBMocks::getInstance();



class DBMocks{

	private static $_instance;
	private static $_bound = array();
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
		$mysql = new SaeMysql();

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
		
		
		$sql = "SELECT * FROM ".DEVICEDATA. " LIMIT 10";
		$data = $mysql->getLine( $sql );
		$id = strip_tags( $_REQUEST['id'] );
		$deviceid = intval( $_REQUEST['deviceid'] );
		//$sql = "INSERT  INTO ".DEVICEDATA." ( `userid` , `deviceid` , `msgtype` ) "." VALUES "." ( '20151130' , '0' , '" .MsgType::MSG_UNREAD. "' ) ";
		
		$mysql->runSql( $sql );

		
		if( $mysql->errno() != 0 )
		{
    		die( "Error:" . $mysql->errmsg() );
		}
		else{
			echo json_encode($data);
		}

		/*
		
		// 连主库
		$link = mysqli_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

		// 连从库
		// $link=mysql_connect(SAE_MYSQL_HOST_S.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
		
		if($link)
		{
		    mysql_select_db(SAE_MYSQL_DB,$link);
    		//your code goes here
		    $mysql->runSql( $sql );
		}
		
		*/
		$mysql->closeDb();

	}

	public static function queryBoundInfo()
	{

	}

	public static function queryStatus($deviceID)
	{
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
		return $_status;
	}
	public static function changeStatus($deviceID)
	{

	}

	public static function saveBoundInfo()
	{

	}

	public static function removeBoundInfo()
	{
		
	}
}


?>
