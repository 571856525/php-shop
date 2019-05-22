<?php
/**
 * 发货
 */
class Service_Delivery
{
    private $dDelivery;

    public function __construct()
    {
        $this->dDelivery = new Dao_Delivery();
    }
    /**
     * 根据id获取用户发货信息
     */
    public function getById($id)
    {
        return $this->dDelivery->getById($id);
    }
    /**
     * 根据用户id获取用户发货列表
     */
    public function getList($userid,$pn,$rn)
    {
        return $this->dDelivery->getList($userid,$pn,$rn);
    }
    /**
     * 根据sn获取用户发货信息
     */
    public function getByOrder($ordersn)
    {
        return $this->dDelivery->getByOrder($ordersn);
    }


    /**
     * 根据id获取用户发货信息
     */
    public function getListByUserid($userid)
    {
        return $this->dDelivery->getListByUserid($userid);
    }
    
}
