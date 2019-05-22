<?php

/**
 * 购物车添加修改
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_Trade_Create extends Blue_Commit
{
    private $dTrade;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dTrade= new Dao_Trade();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        //添加
        $this->dTrade->insert($req,true);
    }
}