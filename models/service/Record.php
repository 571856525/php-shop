<?php

class Service_Record
{
    private $dRecord;

    public function __construct()
    {
        $this->dRecord = new Dao_Record();
    }

    /**
     * 根据用户id获取用户收支列表
     */
    public function getList($userid)
    {
        return $this->dRecord->getList($userid);
    }
    
    /**
     * 根据id获取用户收支列表
     */
    public function getById($id)
    {
        return $this->dRecord->getById($id);
    }
    /**
     * 按时间查询我的收支列表
     */
    public function getListBydate($userid,$month,$year)
    {
        return $this->dRecord->getListBydate($userid,$month,$year);
    }
    
     /**
     * 根据用户id获取用户收支列表
     */
    public function getListByType($type,$userid,$year,$month)
    {
        return $this->dRecord->getListByType($type,$userid,$year,$month);
    }

     /**
     * 根据用户id获取用户收支列表
     */
    public function getAllList($userid,$pn, $rn)
    {
        return $this->dRecord->getAllList($userid,$pn, $rn);
    }



     
     /**
     * 根据用户id获取某个月出入总和
     */
    public function getSumByType($type,$userid,$year,$month)
    {
        return $this->dRecord->getSumByType($type,$userid,$year,$month);
    }

}
