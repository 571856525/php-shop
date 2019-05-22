<?php

class Dao_Transfers extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'transfers');
    }
     /**
     * 根据ID获取调拨信息
     */
    public function getById($id)
    {
        return $this->selectOne(" id='".$id."' ", '*');
    }
    /**
     * 根据用户id获取用户调拨信息列表
     */
    public function getByUserId($userid)
    {
        return $this->select(' fromid ='.$userid.' or userid='.$userid, '*', 'order by id desc');
    }
     /**
     * 根据用户id获取用户调拨信息列表
     */
    public function getOrderByUserId($userid,$state)
    {
        if(is_null($state)){
            return $this->select(' fromid =0 and userid='.$userid, '*', 'order by id desc');
        }else{
            return $this->select(' fromid =0 and state='.$state.' and  userid='.$userid, '*', 'order by id desc');
        }
        
    }

    /**
     * 根据上下级调拨信息列表(去除系统调拨)
     */
    public function getListById($userid)
    {
        return $this->select('fromid!=0 and ( fromid ='.$userid.' or userid='.$userid.')', '*', 'order by id desc');
    }
    /**
     * 根据用户id获取下级用户调拨信息列表
     */
    public function getDownByUserId($userid)
    {
        return $this->select(sprintf(' fromid=%d', $userid), '*', 'order by id desc');
    }
    /**
     * 根据订单号获取用户调拨信息列表
     */
    public function getBySn($ordersn)
    {
        return $this->selectOne(" ordersn='".$ordersn."' ", '*');
    }
    /**
     * 根据订单号获取用户调拨信息列表
     */
    public function getMonthByid($id)
    {
        return $this->selectOne("state>=1 and status=1 and userid=".$id."  and  DATE_FORMAT(FROM_UNIXTIME(`addtime`),'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m')", 'sum(amount) as amount');
    }

    public function getListByPlatform($id,$pn,$rn)
    {
        return $this->select(' fromid =0 and state>= 1  and  userid='.$id, '*', sprintf('order by id desc limit %d,%d', ($pn - 1) * $rn, $rn));
    }

    public function getAllListById($id,$type,$pn,$rn,$audit){
        core_log::debug('------*******'.$audit.'-------------'.$type);
        if($audit==null){
           if($type==1){
              return $this->select(' fromid!=0 and state>= 1  and  userid='.$id, '*', sprintf('order by id desc limit %d,%d', ($pn - 1) * $rn, $rn));
           }else if ($type==2){
              return $this->select(' state>= 1  and  fromid='.$id, '*', sprintf('order by id desc limit %d,%d', ($pn - 1) * $rn, $rn));
           }
        }else{
            if($type==1){
                return $this->select(' fromid!=0 and state>= 1  and  userid='.$id.' and audit='.$audit, '*', sprintf('order by id desc limit %d,%d', ($pn - 1) * $rn, $rn));
            }else if ($type==2){
                return $this->select(' state>= 1  and  fromid='.$id.' and audit='.$audit, '*', sprintf('order by id desc limit %d,%d', ($pn - 1) * $rn, $rn));
            }
        }
    }
     /**
     * 向平台调拨、向上级调拨且同意、下级向我调拨且同意
     */
    public function getListByUserId($userid)
    {
        return $this->select(' state>=1 and  (userid='.$userid.' and  fromid=0)  or (fromid ='.$userid.' and audit=1) or (userid='.$userid.' and fromid!=0  and audit=1)', '*', 'order by id desc');
    }
}
