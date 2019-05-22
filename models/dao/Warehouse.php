<?php

class Dao_Warehouse extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'warehouse');
    }
    //根据用户ID获取用户云仓信息
    public function getById($id)
    {
        return $this->select(sprintf('userid=%d and on_inventory>0', $id), '*','group by goodsid');
    } 
    //根据ID获取用户云仓信息
    public function getBywId($id)
    {
        return $this->selectOne(sprintf('id=%d', $id), '*');
    } 
    //根据用户ID和商品ID获取用户云仓信息
    public function getBygoodsId($userid,$goodsid)
    {
        return $this->selectOne('userid='.$userid.' and goodsid='.$goodsid, '*');
    } 
}
