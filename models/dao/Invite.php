<?php
/*
用户关联关系
*/
class Dao_Invite extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'invite');
    }
    /**
     * 根据uid获取关联
     */
    public function get($id)
    {
        return $this->selectOne(sprintf('uid=%d and end >0', $id), '*');
    }
    /**
     * 根据uid获取关联记录
     */
    public function getById($id)
    {
        return $this->selectOne(sprintf('uid=%d and end >0', $id), '*');
    }
    /**
     * 根据uid获取下级会员
     */
    public function getDownlist($id)
    {
        return $this->select(sprintf('rid=%d and end >0 ', $id), '*');
    }
}
