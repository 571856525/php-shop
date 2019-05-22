<?php

class Service_Comments
{
    private $dComments;

    public function __construct()
    {
        $this->dComments = new Dao_Comments();
    }

    /**
     * 获取产品评论列表
     */
    public function getList($goodsid,$pn,$rn){
        return $this->dComments->getList($goodsid,$pn,$rn);
    }

    /**
     * 根据ID获取信息
     */
    public function getById($id)
    {
        return $this->dComments->getById($id);
    }
    /**
     * 根据ID获取总数
     */
    public function getCount($goodsid)
    {
        return $this->dComments->getCount($goodsid);
    }
}
