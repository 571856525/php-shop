<?php
/**
 * 会员关联
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */
class Commit_User_Invite extends Blue_Commit{
    private $dUser;
    private $dInvite;
	protected function __register(){
		$this->transDB = array('shop');
	}
	protected function __prepare(){
        $this->dUser = new Dao_User();
        $this->dInvite = new Dao_Invite();
	}
	protected function __execute(){
        $req = $this->getRequest();
        $invite = $this->dInvite->getById($req['uid']);
        //写入修改关联
        if(!empty($invite))
        {
            $this->dInvite->update("id=".$invite['id'], $req);
        }
        else
        {
            $this->dInvite->insert($req, true);
        }
        //升级会员
        $user= $this->dUser->getById($req['rid']);
        if($user['reward']<2)
        {   
            $this->dUser->update("id=".$user['id'], array('reward'=>2));
        }
    }
}
