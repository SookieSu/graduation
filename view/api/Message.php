<?php

class Message
{
	public $id;
	public $msgtype;
	public $reg_date;
	public $deviceid;
	public $userid;
	public $data;
	public $isread;

	public function __construct($id,$msgtype,$userid,$deviceid,$data,$reg_date,$isread)
	{
		$this->$id = $id;
		$this->$userid = $userid;
		$this->$deviceid = $deviceid;
		$this->$msgtype = $msgtype;
		$this->$data = $data;
		$this->$reg_date = $reg_date;
		$this->isread = $isread;
	}
}
?>