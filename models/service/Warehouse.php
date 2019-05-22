<?php

class Service_Warehouse
{
    private $dWarehouse;

    public function __construct()
    {
        $this->dWarehouse = new Dao_Warehouse();
    }
    //根据用户ID获取云仓信息
    public function getById($id)
    {
        return $this->dWarehouse->getById($id);
    }
    //根据ID获取云仓信息
    public function getBywId($id)
    {
        return $this->dWarehouse->getBywId($id);
    }
    //根据用户ID和商品ID获取用户云仓信息
    public function getBygoodsId($userid,$goodsid)
    {
        return $this->dWarehouse->getBygoodsId($userid,$goodsid);
    }
    
}