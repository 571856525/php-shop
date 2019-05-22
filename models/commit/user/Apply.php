<?php

/**
 * 提现
 */

class Commit_User_Apply extends Blue_Commit
{
	private $dApply;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dApply = new Dao_Apply();
    }
    protected function __execute()
    {
		$req = $this->getRequest();
        $this->dApply->insert($req, true);
    }
}
