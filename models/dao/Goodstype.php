<?php

class Dao_Goodstype extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'goods_type');
    }

    /**
     * 获取分类列表
     */
    public function getList($classid)
    {
        return $this->select('status=1 and pid='.$classid, '*','order by sort asc');
    }

    /**
     * 根据ID获取分类信息
     */
    public function getById($id)
    {
        return $this->selectOne(sprintf('id=%d', $id), '*');
    }

}
