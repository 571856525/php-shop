<?php

class Service_Goods
{
    private $dGoods;

    public function __construct()
    {
        $this->dGoods = new Dao_Goods();
    }

    /**
     * 获取产品列表
     */
    public function getList($classid,$pn,$rn){
        return $this->dGoods->getList($classid,$pn,$rn);
    }

    /**
     * 根据ID获取信息
     */
    public function getById($id)
    {
        return $this->dGoods->getById($id);
    }
    /**
     * 根据ID获取总数
     */
    public function getCount($classid)
    {
        return $this->dGoods->getCount($classid);
    }
    
     /**
     * 根据ID获取信息
     */
    public function getInfo($id)
    {
        return $this->dGoods->getInfo($id);
    }

}
