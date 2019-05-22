<?php

/**
 * 永久奖励奖励
 */

class Dao_Rewardtemp  extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'reward_temp');
    }

    /**
     * 根据等级获取提成奖励
     */
    public function get($id)
    {
        $ret = $this->selectOne("id=".$id, '*');
        return empty($ret) ? array() : $ret;
    }
    /**
     * 根据等级获取提成奖励
     */
    public function getlist()
    {
        $ret = $this->select(' status =0', '*','order by id asc');
        return empty($ret) ? array() : $ret;
    }
    
}
