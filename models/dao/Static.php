<?php

/**
 * 销量
 */

class Dao_Static  extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'static');
    }

    /**
     * 根据gid获取销量
     */
    public function get($gid)
    {
        $ret = $this->selectOne("gid=".$gid, '*');
        return empty($ret) ? array() : $ret;
    }

    
}
