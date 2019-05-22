<?php

/**
 * 个人微信购买失败
 */

class Commit_Order_Failure extends Blue_Commit
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
        $this->dGoods = new Dao_Goods();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        //修改订单状态
        core_log::debug('看进来没有');
        if($req['ordersn']){
            $order=$this->dOrder->selectOne("ordersn='".$req['ordersn']."' ",'*');
            $cart=$this->dCart->select('orderid='.$order['id'],'*');
            foreach ($cart as &$value) {    
                $goods=$this->dGoods->selectOne('id='.$value['goodsid'],'id,stock');
                $this->dGoods->update('id='.$value['goodsid'],array('stock' => $goods['stock']+$value['num']));
            }
        }
    }
}
