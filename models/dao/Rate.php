<?php

/**
 * 月度利率
 */

class Dao_Rate extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'rate');
    }

    /**
     * 获取利率
     */
    public function getcode($code)
    {
        $ret = $this->selectOne("num<=".$code.' order by num desc', '*');
        return empty($ret) ? array() : $ret;
    }
}
