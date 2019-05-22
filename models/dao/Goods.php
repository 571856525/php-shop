<?php

class Dao_Goods extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'goods');
    }

    /**
     * 获取产品列表
     */
    public function getList($classid,$pn,$rn)
    {
        if($classid)
        {
            return $this->select('status=1 and  classid='.$classid, '*', sprintf('order by sort asc,addtime desc limit %d,%d', ($pn - 1) * $rn, $rn));
        }
        else
        {
            return $this->select('status=1', '*', sprintf('order by sort asc,addtime desc limit %d,%d', ($pn - 1) * $rn, $rn));
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
        if($classid)
        {
            return count($this->select('status=1 and  classid='.$classid, '*', ' '));
        }
        else
        {
            return count($this->select(' status=1 ', '*', ' '));
        }
    }

    /**
     * 根据ID获取信息
     */
    public function getInfo($id)
    {
        return $this->selectOne(sprintf('id=%d', $id), 'goodsname,goodspic,amount');
    }
}
