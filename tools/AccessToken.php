<?php

class AccessToken //implements Serializable 
{

	//private static $serialVersionUID = 1;

	private $access_token;// 令牌
	private $expires_in;// 有效时长 单位秒
	private $createTime;// 创建时间 单位毫秒

	public function getAccess_token() {
		return $access_token;
	}

	public function setAccess_token($access_token) {
		self::$access_token = $access_token;
	}

	public function getExpires_in() {
		return $expires_in;
	}

	public function setExpires_in($expires_in) {
		self::$expires_in = $expires_in;
	}

	public function getCreateTime() {
		return $createTime;
	}

	public function setCreateTime($createTime) {
		self::$createTime = $createTime;
	}

	public static function fromJson($json) {
		 $token =  json_decode($json);
		 $token.setCreateTime(time());
		 return $token;
	}

}

?>