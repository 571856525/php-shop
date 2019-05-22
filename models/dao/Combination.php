<?php

/**
 * 促销，秒杀
 */

class Dao_Combination extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'combination');
    }

    /**
     * 获取促销商品列表
     */
    public function getList($goodsid)
    {
        $ret = $this->select("is_start =1 and status=1 and (goods_id1=$goodsid  or goods_id2=$goodsid or goods_id3=$goodsid or goods_id4=$goodsid or goods_id5=$goodsid )",'*');
        return empty($ret) ? array() : $ret;
    }

    /**
     * 
    */
    public function get($id)
    {
        $ret = $this->selectOne("is_start =1 and status=1 and id=".$id,'*');
        return empty($ret) ? array() : $ret;
    }
    
}
