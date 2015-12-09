<?php

class AccessToken //implements Serializable 
{

	//private static $serialVersionUID = 1;

	private $access_token;// 令牌
	private $expires_in;// 有效时长 单位秒
	private $createTime;// 创建时间 单位毫秒
	
	public function __get($property_name)
	{
		if(isset($this->$property_name))
		{
			return($this->$property_name);
		}
		else
		{
			return(NULL);
		}
	}
	//__set()方法用来设置私有属性
	public function __set($property_name, $value)
	{
		$this->property_name = $value;
	}	

	public function getAccess_token() {
		return $this->access_token;
	}

	public function setAccess_token($at) {
		$this->access_token = $at;
	}

	public function getExpires_in() {
		return $this->expires_in;
	}

	public function setExpires_in($ei) {
		$this->expires_in = $ei;
	}

	public function getCreateTime() {
		return $this->createTime;
	}

	public function setCreateTime($cT) {
		$this->createTime = $cT;
	}

}

?>