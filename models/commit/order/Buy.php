<?php

/**
 * 直购订单创建
 */

class Commit_Order_Buy extends Blue_Commit
{
    private $dOrder;
    private $dCart;

    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dOrder = new Dao_Order();
        $this->dCart = new Dao_Cart();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $this->dOrder->insert($req, true);
    }
}
