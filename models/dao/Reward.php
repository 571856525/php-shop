<?php

/**
 * 等级奖励
 */

class Dao_Reward  extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'reward');
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
    public function getOne($id)
    {
        $ret = $this->selectOne("id=".$id, 'id,typename');
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


        /**
     * 根据等级获取提成奖励
     */
    public function getOnelist()
    {
        $ret = $this->select(' status =0', 'id,typename','order by id asc');
        return empty($ret) ? array() : $ret;
    }
    
}
