<?php

/**
 * 提现记录
 */

class Dao_Apply extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'apply');
    }

    /**
     * 获取我的提现记录
     */
    public function getList($userid)
    {
        $ret = $this->select("status=0 and userid=".$userid, '*', 'order by id desc ');
        return empty($ret) ? array() : $ret;
    }
    /**
     * 获取我的提现数目
     */
    public function getCount($userid)
    {
        $ret = $this->selectCount("status=0 and userid=".$userid, '*', 'order by id desc ');
        return empty($ret) ? array() : $ret;
    }

    public function getUnaudited($userid){
        $ret = $this->select("status=0 and audit=1 and userid=".$userid, '*', 'order by id desc ');
        return empty($ret) ? array() : $ret;
    }
}
