<?php

class Service_Invite
{
    private $dInvite;

    public function __construct()
    {
        $this->dInvite = new Dao_Invite();
    }
    /**
     * 根据ID获取关联关系
     */
    public function get($id)
    {
        return $this->dInvite->get($id);
    }
    /**
     * 根据ID获取关联数据
     */
    public function getById($id)
    {
        return $this->dInvite->getById($id);
    }
    /**
     * 根据ID获取关联数据
     */
    public function getDownlist($id)
    {
        return $this->dInvite->getDownlist($id);
    } 
}
