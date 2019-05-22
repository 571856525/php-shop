<?php

class Service_Returns
{
    private $dReturns;

    public function __construct()
    {
        $this->dReturns = new Dao_Returns();
    }

    /**
     * 根据用户id获取用户收支列表
     */
    public function getList($userid)
    {
        return $this->dReturns->getList($userid);
    }
    
    /**
     * 根据id获取用户收支列表
     */
    public function getById($id)
    {
        return $this->dReturns->getById($id);
    }

     /**
     * 根据id获取用户收支列表
     */
    public function getByOrder($id)
    {
        return $this->dReturns->getByOrder($id);
    }

}
