<?php

/**
 * 调拨申请
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_Transfers_Create extends Blue_Commit
{
	private $dTransfers;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dTransfers = new Dao_Transfers();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $this->dTransfers->insert($req, true);
    }
}
