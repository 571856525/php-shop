<?php
/**
 * 签到
 * 
 */
class Commit_Members_Sign extends Blue_Commit
{
	public function __register(){
		$this->transDB = array('pingwords');
	}

	public function __prepare(){
		$this->dMembers = new Dao_Members();
	}

	public function __execute(){
		$req = $this->getRequest();
		$id = $req['id'];
		unset($req['id']);
		unset($req['sdk']);
		$this->dMembers->update(sprintf('id=%d', $id), $req);
	}
}


