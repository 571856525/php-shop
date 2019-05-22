<?php

class Service_Goodstype
{
    private $dGoodstype;

    public function __construct()
    {
        $this->dGoodstype = new Dao_Goodstype();
    }

    /**
     * 获取列表
     */
    public function getList($classid){
        return $this->dGoodstype->getList($classid);
    }

    /**
     * 根据ID获取信息
     */
    public function getById($id)
    {
        return $this->dGoodstype->getById($id);
    }

}
