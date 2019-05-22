<?php

//组合套餐
class Service_Combination
{
    private $dCombination;

    public function __construct()
    {
        $this->dCombination = new Dao_Combination();
    }

    /**
     * 根据金额获取利率
     */
    public function getlist($goodsid)
    {
        return $this->dCombination->getlist($goodsid);
    }

    /**
     * 
     */
    public function get($id)
    {
        return $this->dCombination->get($id);
    }

}
