<?php

/**
 * 收货地址添加修改
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_Address_Create extends Blue_Commit
{
	private $dAddress;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dAddress = new Dao_Address();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $this->dAddress->insert($req, true);
    }
}
