<?php

class Dao_Comments extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'comments');
    }

    /**
     * 获取商品评论列表
     */
    public function getList($goodsid,$pn,$rn)
    {
        return $this->select('status=2 and  goodsid='.$goodsid, '*', sprintf('order by create_time desc limit %d,%d', ($pn - 1) * $rn, $rn));
    }

    /**
     * 根据ID获取评论信息
     */
    public function getById($id)
    {
        return $this->selectOne(sprintf('id=%d', $id), '*');
    }
    /**
     * 根据ID获取商品评论总数
     */
    public function getCount($goodsid)
    {
        return count($this->select('status=2 and  goodsid='.$goodsid, '*', ' '));
    }
}
