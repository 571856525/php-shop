<?php

/**
 * 月度总销量
 */

class Dao_Sales extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'sales');
    }

    /**
     * 获取销量列表
     */
    public function getList($pn, $rn, $month,$year)
    {
        $ret = $this->select("month=".$month." and year=".$year, '*', sprintf('order by id desc limit %d,%d', ($pn - 1) * $rn, $rn));
        return empty($ret) ? array() : $ret;
    }

    /**
     * 获取销量总量
     */
    public function getCount($month,$year)
    {
        $ret = $this->select("month=".$month." and year=".$year, 'id');
        return count($ret);
    }
    /**
     * 通过id获取销量信息
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        $ret = $this->selectOne(sprintf('id=%s', $id), '*');
        return empty($ret) ? array() : $ret;
    }

    /**
     * 通过用户ID,月份,年份获取销量信息
     * @return array
     */
    public function getByIdMonth($id,$month,$year)
    {
        $ret = $this->selectOne(" month=".$month." and year=".$year." and userid=".$id, '*');
        return $ret;
    }
    /**
     * 通过用户ID,月份,年份获取下级销量信息
     * @return array
     */
    public function getListByIdMonth($id,$month,$year)
    {
        $ret = $this->select(" month=".$month." and year=".$year." and userid in(select id from user where referees=".$id." )", '*');
        return empty($ret) ? array() : $ret;
    }
}
