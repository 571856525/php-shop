<?php
/**
 * 合伙人/商学院申请
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */
class Commit_User_Approval extends Blue_Commit{
	private $dApproval;
	protected function __register(){
		$this->transDB = array('shop');
	}
	protected function __prepare(){
		$this->dApproval = new Dao_Approval();
	}
	protected function __execute(){
		$req = $this->getRequest();
		$this->dApproval->insert($req,true);
	}
}
