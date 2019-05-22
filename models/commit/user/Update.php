<?php
/**
 * 会员修改
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */
class Commit_User_Update extends Blue_Commit{
	private $dUser;
	protected function __register(){
		$this->transDB = array('shop');
	}
	protected function __prepare(){
		$this->dUser = new Dao_User();
	}
	protected function __execute(){
		$req = $this->getRequest();
		$openid=$req['openid'];
		unset($req['openid']);
		$this->dUser->update("openid='".$openid."'", $req);
	}
}
