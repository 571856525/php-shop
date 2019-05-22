<?php

class Service_Carts
{
    private $dCarts;

    public function __construct()
    {
        $this->dCarts = new Dao_Carts();
    }

    /**
     * 根据用户id获取用户购物车列表
     */
    public function getByUserId($userid)
    {
        return $this->dCarts->getByUserId($userid);
    }
    /**
     * 根据订单id获取用户购物车列表
     */
    public function getByOrderId($orderid)
    {
        return $this->dCarts->getByOrderId($orderid);
    }
    /**
     * 根据id获取用户购物车列表
     */
    public function getId($id)
    {
        return $this->dCarts->getId($id);
    }
    /**
     * 根据id获取用户购物车列表
     */
    public function getById($id)
    {
        return $this->dCarts->getById($id);
    }
    /**
     * 根据商品id获取购物车信息
     */
    public function getBygoods($id)
    {
        return $this->dCarts->getBygoods($id);
    }
    /**
     * 根据商品id和用户ID获取购物车信息
     */
    public function getByUserGoods($id,$userid)
    {
        return $this->dCarts->getBygoods($id,$userid);
    }
}
