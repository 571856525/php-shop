<?php

class Service_Cart
{
    private $dCart;

    public function __construct()
    {
        $this->dCart = new Dao_Cart();
    }

    /**
     * 根据用户id获取用户购物车列表
     */
    public function getByUserId($userid)
    {
        return $this->dCart->getByUserId($userid);
    }
    /**
     * 根据id获取用户购物车列表
     */
    public function getId($id)
    {
        return $this->dCart->getId($id);
    }
    /**
     * 根据id获取用户购物车列表
     */
    public function getById($id)
    {
        return $this->dCart->getById($id);
    }
    /**
     * 根据商品id获取购物车信息
     */
    public function getBygoods($id)
    {
        return $this->dCart->getBygoods($id);
    }
    /**
     * 根据商品id和用户ID获取购物车信息
     */
    public function getByUserGoods($id,$userid)
    {
        return $this->dCart->getBygoods($id,$userid);
    }  
    /**
     * 根据订单id获取购物车列表
     */
    public function getByOrderId($id)
    {
        return $this->dCart->getByOrderId($id);
      
    }
}
