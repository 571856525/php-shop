<?php

class Service_Rate
{
    private $dRate;

    public function __construct()
    {
        $this->dRate = new Dao_Rate();
    }

    /**
     * 根据金额获取利率
     */
    public function getcode($code)
    {
        return $this->dRate->getcode($code);
    }

}
