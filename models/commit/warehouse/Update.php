<?php

/**
 * 云仓更新
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_Warehouse_Update extends Blue_Commit
{
	private $dWarehouse;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dWarehouse = new Dao_Warehouse();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $this->dWarehouse->update(sprintf('userid=%d', $req['userid']), $req);
    }
}
