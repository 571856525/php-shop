<?php

class Dao_Order extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'order');
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
    /**
     * 根据用户id和status获取用户订单列表
     */
    public function getOrders($userid,$pn,$rn,$state)
    {
        if(is_null($state)){
           //$status为空则为全部订单
           return $this->select(sprintf('status=1 and userid=%d', $userid), '*', sprintf('order by id desc limit %d,%d', ($pn - 1) * $rn, $rn));
        }else{
           return $this->select(sprintf('status=1 and  state=%d and userid=%d',$state,$userid), '*', sprintf('order by id desc limit %d,%d', ($pn - 1) * $rn, $rn));
        }
    }
    /**
     * 根据订单编号订单信息
     */
    public function getByOrder($ordersn)
    {
        return $this->selectOne("ordersn='".$ordersn."' ", '*');
    }
    /**
     * 根据订单编号订单信息
     */
    public function getMonthByid($id)
    {
        return $this->selectOne("state>=1 and status=1 and userid=".$id."  and  DATE_FORMAT(FROM_UNIXTIME(`addtime`),'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m')", 'sum(amount) as amount');
    }
    /**
     * 根据userid查询月份总金额
     */
    public function getAmountById($id,$year,$month)
    {
 
        return $this->selectOne("state>=1 and userid=".$id." and FROM_UNIXTIME(addtime,'%m') = ".$month." and FROM_UNIXTIME(addtime,'%Y') = ".$year, 'sum(amount) as amount');
    }
    
    /**
     * 根据userid查询总金额
     */
    public function getTeamAmount($id)
    {
        return $this->selectOne("state>=1 and userid in (".$id.")", 'sum(amount) as amount');
    }

}
