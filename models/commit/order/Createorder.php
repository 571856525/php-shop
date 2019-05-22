<?php

/**
 * 
 */

class Commit_Order_Createorder extends Blue_Commit
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
        $this->dGoods= new Dao_Goods();
        $this->dCombination= new Dao_Combination();
        $this->dPromotion= new Dao_Promotion();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $data=json_decode($req['data']);
        var_dump($data);die;
        unset($req['data']);
        foreach($data['id'] as $k=>$v)
        {
            $ware=$this->sWarehouse->getBywId($v);
            if($ware['on_inventory']<$req['num'][$k])
            {
                $this->Warning('库存不足!!');
            } 
            if($req['num'][$k]>0){
                $data['num']=$req['num'][$k];
                $data['id']=$ware['goodsid'];
                $goods[]=$data;
            }  
        }
    }
}
