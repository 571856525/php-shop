<?php

/**
 * 会员写入
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_User_Create extends Blue_Commit
{
	private $dUser;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dUser = new Dao_User();
    }
    protected function __execute()
    {
		$req = $this->getRequest();
        $this->dUser->insert($req, true);
    }
}
