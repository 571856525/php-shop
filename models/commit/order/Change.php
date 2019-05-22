<?php

/**
 * 购物车更新
 */

class Commit_Order_Change extends Blue_Commit
{
    private $dCart;
    private $dGoods;
    private $dOrder;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dCart = new Dao_Cart();
        $this->dGoods = new Dao_Goods();
        $this->dOrder = new Dao_Order();
        $this->dUser = new Dao_User();
        $this->dReward = new Dao_Reward();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $order = $this->dOrder->getByOrder($req['ordersn']);
        if($req['id'])
        {
            foreach($req['id'] as $k=>$v)
            {  
                $cart= $this->dCart->getId($v);
                //获取商品信息
                $sg=$this->dGoods->getById($cart['goodsid']);
                $user=$this->dUser->getById($req['userid']);
                if($user['reward']>1){
                   $reward=$this->dReward->get($user['reward']); 
                   $sg['amount'] =$reward['re_price'];  
                }
                $data=array(
                    'orderid'=>$order['id'],
                    'goodsid'=>$cart['goodsid'],
                    'amount'=>  $sg['amount']* $req['num'][$k],
                    'num'=>$req['num'][$k]
                );
                $this->dCart->update(sprintf('id=%d', $cart['id']), $data);
            }
        }
        
    }
}
