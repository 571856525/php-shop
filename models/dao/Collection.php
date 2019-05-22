<?php
/**
*  收藏
*/
class Dao_Collection extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'collection');
    }

    /**
     * 取出该用户的商品收藏列表
     */
    public function getList($userid,$pn,$rn)
    {
        return $this->select('status=1 and  userid='.$userid, '*', sprintf('order by create_time desc limit %d,%d', ($pn - 1) * $rn, $rn));
    }
    /**
     * 取出该用户的商品收藏
     */
    public function getCount($userid)
    {
        return count($this->select('status=1 and  userid='.$userid, '*', ' '));
    }

     /**
     * 取出该用户的商品收藏
     */
    public function getOne($userid,$goodsid)
    {
       return $this->selectOne(sprintf('status=1 and userid=%d and goodsid=%d',$userid,$goodsid), 'id');
    }
}
