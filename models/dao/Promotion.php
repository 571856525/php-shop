<?php

/**
 * 促销，秒杀
 */

class Dao_Promotion extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'promotion');
    }

    /**
     * 获取促销商品列表
     */
    public function getList($type,$pn, $rn)
    {
        $ret = $this->select("status=1 and type=".$type,'*', sprintf('order by sort asc,create_time desc limit %d,%d', ($pn - 1) * $rn, $rn));
        return empty($ret) ? array() : $ret;
    }

     /**
     * 获取促销商品列表
     */
    public function getCount($type)
    {
        $ret = $this->select("status=1 and type=".$type);
        return count($ret);
    }


    /**
     * 
    */
    public function get($id)
    {
        $ret = $this->selectOne("id=".$id,'*');
        return empty($ret) ? array() : $ret;
    }

    public function getById($id)
    {
        $ret = $this->selectOne("status=1 and id=".$id,'*');
        return empty($ret) ? array() : $ret;
    }

    /**
     * 
    */
    public function getOne($type,$goodsid,$status)
    {
        $time=time();
        $status=empty($status)? " and start_time<=".$time."  and end_time>".$time: '';
        $ret = $this->selectOne("status=1 and goodsid=".$goodsid." and type=".$type.$status,'*');
        return empty($ret) ? array() : $ret;
    }
    
    
    public function getSpike()
    {
        $ret = $this->select("status=1  and type=1 and end_time>".time(),'*');
        return empty($ret) ? array() : $ret;
    }

    public function getSpikeByGoodsid($goodsid)
    {
        $ret = $this->selectOne("status=1  and type=1 and end_time>".time()." and goodsid=".$goodsid,'*');
        return empty($ret) ? array() : $ret;
    }

    /**
     * 看某一个商品是否是促销商品
    */
    public function getIfOne($goodsid)
    {
        $time=time();
        $status=" and start_time<=".$time."  and end_time>".$time;
        $ret = $this->selectOne("status=1 and goodsid=".$goodsid." ".$status,'*');
        return empty($ret) ? array() : $ret;
    }



    public function getProOne($goodsid,$status)
    {
        $time=time();
        $status=empty($status)? " and start_time<=".$time."  and end_time>".$time: '';
        $ret = $this->selectOne("status=1 and goodsid=".$goodsid." and (type=2 or type=3)  ".$status,'*');
        return empty($ret) ? array() : $ret;
    }


    
    
}
