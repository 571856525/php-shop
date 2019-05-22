<?php

/**
 * 代理商
 */

class Dao_Agent extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'agent');
    }

    /**
     * 获取代理商列表
     */
    public function getList($userid)
    {
        $ret = $this->select('status=1 and  userid='.$userid, '*', 'order by create_time desc');
        return empty($ret) ? array() : $ret;
    }

    /**
     * 获取管理员列表信息
     * @return array
     */
    public function getAgentById($userid,$state)
    {
        return $this->selectOne("status=1  and  userid=".$userid."  and state=".$state, '*');
    }



    /**
     * 获取管理员列表信息
     * @return array
     */
    public function getOneById($userid)
    {
        return $this->selectOne("userid=".$userid."  order by create_time desc", '*');
    }


    
}
