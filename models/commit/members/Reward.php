<?php
/**
 * 学分奖励
 * 
 */
class Commit_Members_Reward extends Blue_Commit
{
	public function __register(){
		$this->transDB = array('pingwords');
	}

	public function __prepare(){
		$this->dMembers = new Dao_Members();
	}

	public function __execute(){
		$req = $this->getRequest();
		$this->dMembers->update(sprintf('openid="%s"', $req['openid']), sprintf('credit=credit+%d,grade=%d', $req['credit'], $req['grade']));
	}
}


