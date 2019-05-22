<?php

/**
 * 素材同步
 */

class Commit_Returns_Create extends Blue_Commit
{
	private $dReturns;

	protected function __register()
	{
		$this->transDB = array('shop');
	}

	protected function __prepare()
	{
		$this->dReturns = new Dao_Returns();
	}

	protected function __execute()
	{
        $req = $this->getRequest();
        if(!empty($req)){
             $this->dReturns->insert($req,true);
        }
	}
}
