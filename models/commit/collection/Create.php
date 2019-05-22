<?php

/**
 * 评论添加
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_Collection_Create extends Blue_Commit
{
    private $dCollection;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dCollection = new Dao_Collection();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        //添加
        if(!empty($req))
        {
            $this->dCollection->insert($req, true);
        }
    }
}
