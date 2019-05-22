<?php

class Service_Collection
{
    private $dCollection;

    public function __construct()
    {
        $this->dCollection = new Dao_Collection();
    }

    /**
     * 获取产品评论列表
     */
    public function getList($userid,$pn,$rn)
    {
        return $this->dCollection->getList($userid,$pn,$rn);
    }

    /**
     * 根据ID获取总数
     */
    public function getCount($userid)
    {
        return $this->dCollection->getCount($userid);
    }

     /**
     * 获取是否收藏该商品
     */
    public function getOne($userid,$goodsid){
        return $this->dCollection->getOne($userid,$goodsid);
    }

}
