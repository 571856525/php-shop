<?php

class Service_Ad
{
    private $dAd;

    public function __construct()
    {
        $this->dAd = new Dao_Ad();
    }

    /**
     * 获取广告列表
     */
    public function getList($cid){
        return $this->dAd->getList($cid);
    }

    /**
     * 根据ID获取广告信息
     */
    public function getById($id)
    {
        return $this->dAd->getById($id);
    }
}
