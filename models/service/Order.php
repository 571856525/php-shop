<?php

class Service_Order
{
    private $dOrder;

    public function __construct()
    {
        $this->dOrder = new Dao_Order();
    }
    /**
     * 根据id获取用户订单信息
     */
    public function getById($id)
    {
        return $this->dOrder->getById($id);
    }
    /**
     * 根据用户id获取用户订单列表
     */
    public function getByUserId($userid)
    {
        return $this->dOrder->getByUserId($userid);
    }
    /**
     * 根据用户id和状态获取用户订单列表
     */
    public function getOrders($userid,$pn,$rn,$state)
    {
        return $this->dOrder->getOrders($userid,$pn,$rn,$state);
    }
    /**
     * 根据sn获取用户订单信息
     */
    public function getByOrder($ordersn)
    {
        return $this->dOrder->getByOrder($ordersn);
    }
    /**
     * 根据UID获取本月业绩
     */
    public function getMonthByid($id)
    {
        return $this->dOrder->getMonthByid($id);
    }
    /**
     * 根据UID当月业绩
     */
    public function getAmountById($id,$year,$month){
        return $this->dOrder->getAmountById($id,$year,$month);
    }

    /**
     * 根据ids总业绩
     */
    public function getTeamAmount($ids){
        return $this->dOrder->getTeamAmount($ids);
    }
}
