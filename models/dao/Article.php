<?php

class Dao_Article extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'article');
    }

    /**
     * 获取产品列表
     */
    public function getList($classid,$pn,$rn)
    {
        if($classid)
        {
            return $this->select('status=1 and  cid='.$classid, '*', sprintf('order by addtime desc limit %d,%d', ($pn - 1) * $rn, $rn));
        }
        else
        {
            return $this->select('status=1', '*', sprintf('order by addtime desc limit %d,%d', ($pn - 1) * $rn, $rn));
        }   
    }

    /**
     * 根据ID获取商品信息
     */
    public function getById($id)
    {
        return $this->selectOne(sprintf('id=%d', $id), '*');
    }
    /**
     * 根据ID获取商品总数
     */
    public function getCount($classid)
    {
        if($id)
        {
            return count($this->select('status=1 and  cid='.$classid, '*', ' '));
        }
        else
        {
            return count($this->select(' status=1 ', '*', ' '));
        }
    }
}
