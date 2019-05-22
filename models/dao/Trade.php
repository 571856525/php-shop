<?php

class Dao_Trade extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'trade');
    }
    /**
     * 根据用户id获取用户订单信息
     */
    public function getById($id)
    {
        return $this->selectOne("id='".$id."' ", '*');
    }
    /**
     * 根据用户id获取用户订单列表
     */
    public function getByUserId($userid)
    {
        return $this->select(sprintf('status>=1 and userid=%d', $userid), '*', 'order by id desc');
    }
    /*
     * 根据订单编号订单信息
     */
    public function getByTrade($tradesn)
    {
        return $this->selectOne("tradesn='".$tradesn."' ", '*');
    }

     /**
     * 根据用户id获取用户充值订单列表
     */
    public function getList($userid)
    {
        return $this->select(sprintf('status=11 and type=1 and userid=%d', $userid), '*', 'order by id desc');
    }
}
