<?php

/**
 * 等级奖励
 */

class Dao_Returns  extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'returns');
    }

    /**
     * 根据等级获取提成奖励
     */
    public function getById($id)
    {
        $ret = $this->selectOne("id=".$id, '*');
        return empty($ret) ? array() : $ret;
    }
    /**
     * 根据等级获取提成奖励
     */
    public function getlist($userid)
    {
        $ret = $this->select('userid ='.$userid, '*','order by id asc');
        return empty($ret) ? array() : $ret;
    }


    public function getByOrder($id)
    {
        $ret = $this->selectOne('orderid ='.$id, 'id,status');
        return empty($ret) ? array() : $ret;
    }
}
