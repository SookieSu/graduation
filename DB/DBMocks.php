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
		$sql = "CREATE TABLE MediaData (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		userid  VARCHAR(30) NOT NULL,
		deviceid VARCHAR(30) NOT NULL,
		msgtype VARCHAR(30) NOT NULL,
		data MEDIUMBLOB,
		reg_date TIMESTAMP
		)";
		self::$mysql->runSql( $sql );
		*/
		$sql = "CREATE TABLE State (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		userid  VARCHAR(30) NOT NULL,
		deviceid VARCHAR(30) NOT NULL,
		state VARCHAR(30) NOT NULL,
		reg_date TIMESTAMP
		)";
		self::$mysql->runSql( $sql );
		//$mysql->closeDb();
	}

	public static function queryBoundInfo($id)
	{
		//suppose userID
		$sql = "SELECT `userID`,`deviceID` FROM " . MsgType::BOUNDDATA . " WHERE userID = '$id'" ;
		$data = self::$mysql->getData( $sql );
		if( self::$mysql->errno() != 0 )
		{
    		die( "Error in queryBoundInfo :" . self::$mysql->errmsg() );
		}
		else if($data == null)
		{
			//suppose deviceID
			$sql = "SELECT `userID`,`deviceID` FROM " . MsgType::BOUNDDATA . " WHERE deviceID = '$id'" ;
			$data = self::$mysql->getData( $sql );
		}
		//此时拿到了含有id的多维数组data
		$_bound = $data;
		echo json_encode($_bound);
		return $_bound;
	}

	public static function queryStatus($table,$deviceID)
	{
		$sql = "SELECT * FROM ".MsgType::$table." LIMIT 10";
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
		return $_status;
	}

	/*
	 * @funcName : queryMessageInfo
 	 * @funcDescription : 查询某表格，某用户id的未读/已读/所有信息，返回一个对象数组
	 * @funcParam : $table,$id,$isread
	*/
	public static function queryMessageInfo($table,$id,$isread = null)
	{
		$retbound = self::queryBoundInfo($id);
		//echo "print retbound ! ".var_dump($retbound);
		if($retbound == null)
		{
			return false;
		}
		foreach ($retbound as $record) 
		{
			$userID = $record['userID'];
			$deviceID = $record['deviceID'];
			if($isread != null)
			{
				$sql = "SELECT * FROM ".$table. " WHERE isread = '$isread' AND deviceid = '$deviceID' AND userid = '$userID' ";
			}
			else{
				$sql = "SELECT * FROM ".$table. " WHERE deviceid = '$deviceID' AND userid = '$userID' ";
			}
			$data = self::$mysql->getData( $sql );
			if( self::$mysql->errno() != 0  )
			{
    			die( "Error in queryMessageInfo :" . self::$mysql->errmsg() );
    			return false;
			}
			else 
			{
				//echo json_encode($data);
				echo var_dump($data);
				return $data;
			}
		}
	}

	/*
	 * @funcName : addMessageInfo
 	 * @funcDescription : 增加某表格，某用户id的未读信息，返回bool类型,该id可以是用户id或设备id
	 * @funcParam : $table,$id,$msgtype,$data
	*/
	public static function addMessageInfo($table,$id,$msgtype,$data)
	{
		$retbound = self::queryBoundInfo($id);
		//echo "print retbound ! ".var_dump($retbound);
		if($retbound == null)
		{
			return false;
		}
		foreach ($retbound as $record) 
		{
			# code...
			$userID = $record['userID'];
			$deviceID = $record['deviceID'];
			$isread = "false";
			if($data != null)
			{
				$tmpdata = self::$mysql->escape($data);
			}
			$sql = "INSERT INTO ".$table."( `userid` ,`deviceid` , `msgtype` , `isread` , `data` ) "." VALUES "." ( '$userID' , '$deviceID' , '$msgtype' , '$isread' , '$tmpdata' ) ";
			//echo "sql : ". $sql;
			self::$mysql->runSql( $sql );
			if( self::$mysql->errno() != 0 )
			{
			    die( "Error in addMessageInfo :" . self::$mysql->errmsg() );
			    return false;
			}
			else
			{
				echo "success addMessageInfo  : ".$table. ":" . $userID . ":" . $deviceID . " ! \n";
				return true;
			}
		}
	}

	public static function addStateInfo($id,$state)
	{
		$retbound = self::queryBoundInfo($id);
		//echo "print retbound ! ".var_dump($retbound);
		if($retbound == null)
		{
			return false;
		}
		foreach ($retbound as $record) 
		{
			# code...
			$userID = $record['userID'];
			$deviceID = $record['deviceID'];
			$sql = "INSERT INTO ".MsgType::STATE."( `userid` ,`deviceid` , `state` ) "." VALUES "." ( '$userID' , '$deviceID' , '$state' ) ";
			//echo "sql : ". $sql;
			self::$mysql->runSql( $sql );
			if( self::$mysql->errno() != 0 )
			{
			    die( "Error in addStateInfo :" . self::$mysql->errmsg() );
			    return false;
			}
			else
			{
				echo "success addStateInfo  : ". $userID . ":" . $deviceID . " ! \n";
				return true;
			}
		}
	}

	public static function queryStateInfo($id)
	{
		$retbound = self::queryBoundInfo($id);
		//echo "print retbound ! ".var_dump($retbound);
		if($retbound == null)
		{
			return false;
		}
		foreach ($retbound as $record) 
		{
			# code...
			$userID = $record['userID'];
			$deviceID = $record['deviceID'];
			$sql = "SELECT state FROM ".MsgType::STATE. " WHERE deviceid = '$deviceID' AND userid = '$userID'";
			$data = self::$mysql->getLine( $sql );
			if( self::$mysql->errno() != 0 )
			{
			    die( "Error in queryStateInfo :" . self::$mysql->errmsg() );
			    return false;
			}
			else 
			{
				//echo json_encode($data);
				echo var_dump($data);
				return $data;
			}
		}
	}
	public static function updateStateInfo($table,$id,$state)
	{
		$retbound = self::queryBoundInfo($id);
		//echo "print retbound ! ".var_dump($retbound);
		if($retbound == null)
		{
			return false;
		}
		foreach ($retbound as $record) 
		{
			# code...
			$userID = $record['userID'];
			$deviceID = $record['deviceID'];
			$sql = "UPDATE ".MsgType::STATE." SET state = '$state' WHERE deviceid = '$deviceID' AND userid = '$userID'";
			//echo $sql;
			self::$mysql->runSql( $sql );
			if( self::$mysql->errno() != 0 )
			{
    			die( "Error in updateStateInfo :" . self::$mysql->errmsg() );
			}
			else
			{
				echo "success update StateInfo ! \n";
				return true;
			}
		}
	}

	public static function deleteStateInfo($id)
	{
		$retbound = self::queryBoundInfo($id);
		//echo "print retbound ! ".var_dump($retbound);
		if($retbound == null)
		{
			return false;
		}
		foreach ($retbound as $record) 
		{
			# code...
			$userID = $record['userID'];
			$deviceID = $record['deviceID'];
			$sql = "DELETE FROM ".MsgType::STATE." WHERE deviceid = '$deviceID' AND userid = '$userID' " ;
			//echo $sql;
			self::$mysql->runSql( $sql );
			if( self::$mysql->errno() != 0 )
			{
    			die( "Error in deleteStateInfo :" . self::$mysql->errmsg() );
    			return false;
			}
			else
			{
				echo "success deleteStateInfo  : ".$id ." ! \n";
				return true;
			}
		}
	}

	public static function addMediaInfo($table,$id,$msgtype,$data)
	{
		$retbound = self::queryBoundInfo($id);
		//echo "print retbound ! ".var_dump($retbound);
		if($retbound == null)
		{
			return false;
		}
		foreach ($retbound as $record) 
		{
			# code...
			$userID = $record['userID'];
			$deviceID = $record['deviceID'];
			if($data != null)
			{
				$tmpdata = self::$mysql->escape($data);
			}
			$sql = "INSERT INTO ".$table."( `userid` ,`deviceid` , `msgtype` , `data` ) "." VALUES "." ( '$userID' , '$deviceID' , '$msgtype' , '$tmpdata' ) ";
			//echo "sql : ". $sql;
			self::$mysql->runSql( $sql );
			if( self::$mysql->errno() != 0 )
			{
			    die( "Error in addMediaInfo :" . self::$mysql->errmsg() );
			    return false;
			}
			else
			{
				echo "success addMediaInfo  : ".$table. ":" . $userID . ":" . $deviceID . " ! \n";
				return true;
			}
		}
	}

	public static function queryMediaInfo($table,$id,$msgtype,$songID = null)
	{
		//echo "id : \n".$id;
		$retbound = self::queryBoundInfo($id);
		//echo "print retbound ! ".var_dump($retbound);
		if($retbound == null)
		{
			return false;
		}
		foreach ($retbound as $record) 
		{
			# code...
			$userID = $record['userID'];
			$deviceID = $record['deviceID'];
			if($songID != null)
			{
				$sql = "SELECT * FROM ".$table. " WHERE deviceid = '$deviceID' AND userid = '$userID' AND msgtype = '$msgtype' AND id = '$songID' ";
			}
			else{
				$sql = "SELECT * FROM ".$table. " WHERE deviceid = '$deviceID' AND userid = '$userID' AND msgtype = '$msgtype'";
			}
			$data = self::$mysql->getData( $sql );
			if( self::$mysql->errno() != 0 )
			{
			    die( "Error in queryMessageInfo :" . self::$mysql->errmsg() );
			    return false;
			}
			else 
			{
				//echo json_encode($data);
				echo var_dump($data);
				return $data;
			}
		}
	}
	/*
	 * @funcName : setMessageReadInfo
 	 * @funcDescription : 设置某表格的某record信息已读，返回bool类型
	 * @funcParam : $table,$recordID
	*/
	public static function setMessageReadInfo($table,$recordID)
	{
		$sql = "UPDATE ".$table." SET isread = 'true' "." WHERE id = '$recordID' ";
		//echo $sql;
		self::$mysql->runSql( $sql );
		if( self::$mysql->errno() != 0 )
		{
    		die( "Error in setMessageReadInfo :" . self::$mysql->errmsg() );
    		return false;
		}
		else
		{
			echo "success set MessageReadInfo  : ".$table. ":" .$recordID ." ! \n";
			return true;
		}
	}

	/*
	 * @funcName : deleteMessageInfo
 	 * @funcDescription : 删除某表格的某record信息，返回bool类型
	 * @funcParam : $table,$recordID
	*/
	public static function deleteMessageInfo($table,$recordID)
	{
		$sql = "DELETE FROM ".$table." WHERE id = '$recordID' " ;
		//echo $sql;
		self::$mysql->runSql( $sql );
		if( self::$mysql->errno() != 0 )
		{
    		die( "Error in deleteMessageInfo :" . self::$mysql->errmsg() );
    		return false;
		}
		else
		{
			echo "success delete MessageInfo  : ".$table. ":" .$recordID ." ! \n";
			return true;
		}
	}
	public static function saveBoundInfo($userID,$deviceID)
	{
		$sql = "INSERT  INTO ".MsgType::BOUNDDATA." ( `userid` , `deviceid` ) "." VALUES "." ( '$userID' , '$deviceID') ";
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
		$sql = "DELETE FROM ".MsgType::BOUNDDATA." WHERE userID = '$userID' AND deviceID = '$deviceID'";
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
		$sql = "UPDATE ".MsgType::ACCESSTOKEN." SET access_token = "."'$access_token'"." WHERE id = '1' ";
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
		$sql = "SELECT * FROM ".MsgType::ACCESSTOKEN. " WHERE id = '1' ";
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

	public static function updateSNSAccessToken($code,$SNSaccess_token)
	{
		//$sql = "INSERT  INTO ".MsgType::SNSACCESSTOKEN." ( `code` , `data` ) "." VALUES "." ( '$code' , '$SNSaccess_token') ";
		//echo "print in update SNSaccess_token : \n";
		//echo var_dump($code);
		//echo var_dump($SNSaccess_token);
		$access_token = json_encode($SNSaccess_token);
		$sql = "UPDATE ".MsgType::SNSACCESSTOKEN." SET code = '$code' , data = '$access_token'  WHERE id = '3' ";
		//echo $sql;
		self::$mysql->runSql( $sql );
		if( self::$mysql->errno() != 0 )
		{
    		die( "Error in updateSNSAccessToken :" . self::$mysql->errmsg() );
		}
		else
		{
			echo "success update SNS access_token ! \n";
			return true;
		}
	}
	public static function querySNSAccessToken()
	{
		$sql = "SELECT * FROM ".MsgType::SNSACCESSTOKEN." WHERE id = '3' ";
		$data = self::$mysql->getLine( $sql );
		
		if( self::$mysql->errno() != 0  )
		{
    		die( "Error in querySNSAccessToken :" . self::$mysql->errmsg() );
		}
		else if ( $data != null){
			//echo json_encode($data);
			//echo var_dump($data);
			return $data;
		}
	}

	public static function Test($method = null,$id = null)
	{
		//$sql = "INSERT  INTO ".MsgType::STATE." ( `code` , `data` ) "." VALUES "." ( 'lalala' , 'llllll') ";
		//$sql = "DELETE FROM ".MsgType::SNSACCESSTOKEN;
		//self::$mysql->runSql( $sql );
		/*if( self::$mysql->errno() != 0  )
		{
    		die( "Error in Test :" . self::$mysql->errmsg() );
		}
		else {
			echo 'ok';
			return true;
		}*/
		switch ($method) {
			case 'queryBoundInfo':
				# code...
				self::queryBoundInfo($id);
				break;
			case 'queryMessageInfo':
				# code...
				self::queryMessageInfo(MsgType::DEVICEDATA,$id);
				break;
			case 'queryMediaInfo':
				# code...
				self::queryMediaInfo(MsgType::MEDIADATA,$id,MsgType::SONG);
				break;
			case 'queryAccessToken':
				# code...
				self::queryAccessToken();
				break;
			case 'querySNSAccessToken':
				# code...
				self::querySNSAccessToken();
				break;
			default:
				# code...
				break;
		}
		
	}
}


?>
