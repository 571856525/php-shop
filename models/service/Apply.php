<?php

class Service_Apply
{
    private $dApply;

    public function __construct()
    {
        $this->dApply = new Dao_Apply();
    }

    /**
     * 根据用户id获取用户提现列表
     */
    public function getList($userid)
    {
        return $this->dApply->getList($userid);
    }
    /**
     * 根据用户id获取未审核提现记录
     */
    public function getUnaudited($userid)
    {
        return $this->dApply->getUnaudited($userid);
    }
    
}
