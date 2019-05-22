<?php

/**
 * 收支记录
 */

class Dao_Record extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'record');
    }

    /**
     * 获取我的收支列表,type=5为邀请记录，为系统消息
     */
    public function getList($userid)
    {
        $ret = $this->select("type!=5 and userid=".$userid, '*', 'order by id desc ');
        return empty($ret) ? array() : $ret;
    }


    /**
     * 获取我的收支记录表
     */
    public function getCount($userid)
    {
        $ret = $this->selectCount("type!=5 and userid=".$userid);
        return empty($ret) ? array() : $ret;
    }
    /**
     * 获取某一个record
     */
    public function getById($id)
    {
        $ret = $this->selectOne("id=".$id, '*');
        return empty($ret) ? array() : $ret;
    }
    /**
     * 按时间查询我的收支列表
     */
    public function getListBydate($userid,$month,$year)
    {
        $ret = $this->select("type!=5 and type!=6 and type!=7 and userid=".$userid." and FROM_UNIXTIME(addtime,'%m') = ".$month." and FROM_UNIXTIME(addtime,'%Y') = ".$year, '*', 'order by id desc ');
        return empty($ret) ? array() : $ret;
    }
     /**
     * 获取我的收支列表
     */
    public function getListByType($type,$userid,$year,$month)
    {
        $ret = $this->select("type=".$type." and userid=".$userid." and FROM_UNIXTIME(addtime,'%m') = ".$month." and FROM_UNIXTIME(addtime,'%Y') = ".$year, '*', 'order by id desc ');
        return empty($ret) ? array() : $ret;
    }

    public function getAllList($userid,$pn,$rn){
        $ret = $this->select("type!=5 and userid=".$userid, '*',  sprintf('order by addtime desc limit %d,%d', ($pn - 1) * $rn, $rn));
        return empty($ret) ? array() : $ret;
    }
    
    /**
     * 获取我的收支列表
     */
    public function getSumByType($type,$userid,$year,$month)
    {
        $ret = $this->selectOne("type=".$type." and userid=".$userid." and FROM_UNIXTIME(addtime,'%m') = ".$month." and FROM_UNIXTIME(addtime,'%Y') = ".$year, 'sum(amount) as sum');
        return empty($ret) ? array() : $ret['sum'];
    }
}
