<?php

/**
 * 购物车添加修改
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_Agent_Create extends Blue_Commit
{
    private $dAgent;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dAgent= new Dao_Agent();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        //添加
        $this->dAgent->insert($req,true);
    }
}