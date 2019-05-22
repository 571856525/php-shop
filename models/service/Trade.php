<?php

class Service_Trade
{
    private $dTrade;

    public function __construct()
    {
        $this->dTrade = new Dao_Trade();
    }
    /**
     * 根据id获取用户订单信息
     */
    public function getById($id)
    {
        return $this->dTrade->getById($id);
    }
    /**
     * 根据用户id获取用户订单列表
     */
    public function getByUserId($userid)
    {
        return $this->dTrade->getByUserId($userid);
    }
    /**
     * 根据sn获取用户订单信息
     */
    public function getByTrade($tradesn)
    {
        return $this->dTrade->getByTrade($tradesn);
    }
    /**
     * 根据用户id获取用户订单列表
     */
    public function getList($userid)
    {
        return $this->dTrade->getList($userid);
    }
}
