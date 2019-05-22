<?php
/**
 * 发货
 */
class Dao_Delivery extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'delivery');
    }
    /**
     * 根据用户id获取用户发货信息
     */
    public function getById($id)
    {
        return $this->selectOne("id='".$id."' ", '*');
    }
    /**
     * 根据用户id获取发货列表
     */
    public function getList($userid,$pn,$rn)
    {
        return $this->select(sprintf('status=1 and userid=%d', $userid), '*', sprintf('order by addtime desc limit %d,%d', ($pn - 1) * $rn, $rn));
    }
    /**
     * 根据订单编号订单信息
     */
    public function getByOrder($ordersn)
    {
        return $this->selectOne("ordersn='".$ordersn."' ", '*');
    }
    
    /**
     * 根据用户id获取发货列表
     */
    public function getListByUserid($userid)
    {
        return $this->select(sprintf('status=1 and userid=%d', $userid), '*', 'order by addtime desc');
    }

}
