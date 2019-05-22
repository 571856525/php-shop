<?php

class Dao_Cart extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'cart');
    }
    /**
     * 根据用户id获取用户购物车列表
     */
    public function getByUserId($userid)
    {
        return $this->select(sprintf('status=1 and orderid=0 and userid=%d', $userid), '*', 'order by id desc');
    }
    /**
     * 根据id获取用户购物车列表
     */
    public function getById($id)
    {
        return $this->select('id in('.$id.')', '*', 'order by id desc');
    }
        /**
     * 根据id获取用户购物车列表
     */
    public function getId($id)
    {
        return $this->selectOne('id ='.$id, '*');
    }
    /**
     * 根据商品id获取购物车信息
     */
    public function getBygoods($id)
    {
        return $this->selectOne(sprintf(' orderid=0 and goodsid=%d', $id), '*');
    }
        /**
     * 根据商品id和用户ID获取购物车信息
     */
    public function getByUserGoods($id,$userid)
    {
        return $this->selectOne(' orderid=0 and userid='.$userid.' and  goodsid='.$id, '*');
    }
    /**
     * 根据订单id获取购物车列表
     */
    public function getByOrderId($id)
    {
       return $this->select('orderid='.$id, '*', 'order by id');
    }
    /**
     * 根据订单ID获取商品总数
     */
    public function getOrderCount($id)
    {
        $order=$this->selectOne('orderid='.$id, 'sum(num) as num');
        return $order['num'];
    }
     
    /**
     * 根据订单ID获取商品总数
     */
    public function getByGoodsid($id,$userid)
    {
        return $this->selectOne(' orderid=0 and userid='.$userid.' and  goodsid='.$id, '*');
    }

}
