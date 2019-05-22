<?php

/**
 * 仓库发货
 */

class Commit_Warehouse_Send extends Blue_Commit
{
    private $dWarehouse;
    private $dDelivery;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dWarehouse = new Dao_Warehouse();
        $this->dDelivery = new Dao_Delivery();
    }
    protected function __execute()
    {
        //添加出货单
        $req = $this->getRequest();
        $this->dDelivery->insert($req,true);
        //更新库存
        if(!empty($req['goods']))
        {
            $goods=json_decode($req['goods'],true);
            foreach($goods as $v)
            {
                $ware= $this->dWarehouse->getBygoodsId($req['userid'],$v['id']);
                $this->dWarehouse->update('goodsid='.$v['id'].' and userid='. $req['userid'], array('on_inventory'=>$ware['on_inventory']-$v['num']));
            }
        }
    }
}
