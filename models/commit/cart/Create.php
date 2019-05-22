<?php

/**
 * 购物车添加修改
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_Cart_Create extends Blue_Commit
{
    private $dCart;
    private $dGoods;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dCart = new Dao_Cart();
        $this->sGoods = new Dao_Goods();
        $this->sUser = new Dao_User ();
        $this->sReward = new Dao_Reward();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $cart= $this->dCart->getByGoodsid($req['goodsid'],$req['userid']);

        //获取商品信息
        $sg=$this->sGoods->getById($req['goodsid']);
        $user=$this->sUser->getById($req['userid']);
 
        $req['amount']= $sg['amount']*$req['num'];
        //添加
        if(empty($cart))
        {
            $this->dCart->insert($req, true);
        }
        //更新
        else
        {
            $this->dCart->update(sprintf('id=%d', $cart['id']), 'num=num+1');
        }
    }
}
