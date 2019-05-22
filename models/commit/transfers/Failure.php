<?php

/**
 * 云仓微信购买失败回调
 * User: Administrator
 * Date: 2018/9/21
 * Time: 18:05
 */

class Commit_Transfers_Failure extends Blue_Commit
{
	private $dTransfers;
	private $dCarts;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dTransfers = new Dao_Transfers();
        $this->dCarts = new Dao_Carts();
        $this->dWarehouse = new Dao_Warehouse();
        $this->dGoods = new Dao_Goods();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        //修改订单状态
        core_log::debug('进入库存问题'.json_encode($req));
        if($req['ordersn']){
            $transfers=$this->dTransfers->selectOne("ordersn='".$req['ordersn']."' ",'*');
            $carts=$this->dCarts->select('orderid='.$transfers['id'],'*');
            foreach ($carts as &$value){
            	if($transfers['fromid']>0){
            		//上级云仓
                    //$warehouse=$this->dWarehouse->selectOne('userid = '.$transfers['fromid'].' and specid='.$value['specid'],'*');
                    //$this->dWarehouse->update('id = '.$warehouse['id'],array('on_inventory' => $warehouse['on_inventory']+$value['num']));


                    //赠送的商品
                    if($value['is_send']){
                        $goods=$this->dGoods->selectOne('id='.$value['goodsid'],'id,stock');
                        $this->dGoods->update('id='.$value['goodsid'],array('stock' => $goods['stock']+$value['num']));
                    }
            	}else{
            		//云仓平台
                    $goods=$this->dGoods->selectOne('id='.$value['goodsid'],'id,stock');
                    $this->dGoods->update('id='.$value['goodsid'],array('stock' => $goods['stock']+$value['num']));
            	}
            }
	    }
    }
}
