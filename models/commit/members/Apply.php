<?php
/**
 * 提现
 * @author xuefei<@yunbix.com>
 */
class Commit_Members_Apply extends Blue_Commit
{
	public function __ragister(){
		$this->transDB = array('pingwrods');
	}

	public function __prepare(){
		$this->dMembersAccount = new Dao_Members_Account();
		$this->dMembers = new Dao_Members();
		$this->dPassport = new Dao_Passport();
	}

	public function __execute(){
		$req = $this->getRequest();
		$this->dMembersAccount->insert($req, true);
		$pass = $this->dPassport->selectOne(sprintf('openid="%s"', $req['openid']));
		if($pass['type'] ==1){
			$this->dMembers->update(sprintf('openid="%s"', $req['openid']), sprintf('bonus=bonus-%d', $req['money']));
		}else{
			$this->dPassport->update(sprintf('openid="%s"', $req['openid']), sprintf('bonus=bonus-%d', $req['money']));
		}
	}
}
