<?php

/**
 * 操作日志
 */

class Dao_Log extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'log');
    }

    /**
     * 获取我的收支列表
     */
    public function getList($userid)
    {
        $ret = $this->select("userid=".$userid, '*', 'order by id desc ');
        return empty($ret) ? array() : $ret;
    }
}
