<?php

class Service_Transfers
{
    private $dTransfers;

    public function __construct()
    {
        $this->dTransfers = new Dao_Transfers();
    }

    /**
     * 根据ID获取调拨信息
     */
    public function getById($id)
    {
        return $this->dTransfers->getById($id);
    }
     /**
     * 根据用户id获取用户调拨信息列表
     */
    public function getByUserId($userid)
    {
        return $this->dTransfers->getByUserId($userid);
    } 
      /**
     * 根据用户id获取用户调拨信息列表
     */
    public function getOrderByUserId($userid,$state)
    {
        return $this->dTransfers->getOrderByUserId($userid,$state);
    }    
    /**
     * 根据上下级调拨信息列表(去除系统调拨)
     */
    public function getListById($userid)
    {
        return $this->dTransfers->getListById($userid);
    }  
     /**
     * 根据用户id获取下级用户调拨信息列表
     */
    public function getDownByUserId($userid)
    {
        return $this->dTransfers->getDownByUserId($userid);
    }   
    /**
     * 根据Sn获取调拨信息
     */
    public function getBySn($sn)
    {
        return $this->dTransfers->getBySn($sn);
    }     
    /**
     * 根据UID获取本月业绩
     */
    public function getMonthByid($id)
    {
        return $this->dTransfers->getMonthByid($id);
    } 

    /**
     * 根据
     */
    public function getListByPlatform($id,$pn,$rn)
    {
        return $this->dTransfers->getListByPlatform($id,$pn,$rn);
    } 

    /**
     * 根据
     */
    public function getAllListById($id,$type,$pn,$rn,$audit)
    {
        return $this->dTransfers->getAllListById($id,$type,$pn,$rn,$audit);
    } 



    public function getListByUserId($userid){
        return $this->dTransfers->getListByUserId($userid);
    }
}
