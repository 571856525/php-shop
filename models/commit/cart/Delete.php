<?php

/**
 * 购物车更新
 */

class Commit_Cart_Delete extends Blue_Commit
{
    private $dCart;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dCart = new Dao_Cart();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        if($req['id']){
              $this->dCart->delete('id='.$req['id']);
        }
    }
}
