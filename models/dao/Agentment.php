<?php

/**
 * 代理商级别
 */

class Dao_Agentment extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'agentment');
    }

    /**
     * 获取代理商级别列表
     */
    public function getAgentList()
    {
        $ret = $this->select('status=1', '*');
        return empty($ret) ? array() : $ret;
    }
}
