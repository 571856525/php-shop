<?php

/**
 * 支付订单创建
 */

class Commit_Order_Delete extends Blue_Commit
{
    private $dOrder;

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
        //更新购物车
        $order = $this->dOrder->getById($req['id']);
        if($order)
        {   
            $this->dOrder->delete('id='.$order['id']);
        }
    }
}
