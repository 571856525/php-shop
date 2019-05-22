<?php

/**
 * 库存更新
 */

class Commit_Goods_Stock extends Blue_Commit
{
    private $dGoods;

    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dGoods = new Dao_Goods();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $goods = $this->dGoods->getById($req['id']); 
        //修改订单状态
        Core_log::Warning('库存更新---------->'.json_encode($req));
        if($goods['stock']>=$req['num']){
            $this->dGoods->update('id='.$req['id'], array('stock'=>$goods['stock']+$req['num']));
        }
    }
}
