<?php

/**
 * 操作日志
 */

class Dao_Profession extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'profession');
    }

    /**
     * 获取我的收支列表
     */
    public function getList()
    {
        $ret = $this->select("status=0",'*', '');
        return empty($ret) ? array() : $ret;
    }


        /**
     * 
     */
    public function get($id)
    {
        $ret = $this->selectOne("id=".$id,'*');
        return empty($ret) ? array() : $ret;
    }
}
