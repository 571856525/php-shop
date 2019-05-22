<?php

class Service_Article
{
    private $dArticle;

    public function __construct()
    {
        $this->dArticle = new Dao_Article();
    }

    /**
     * 获取新闻列表
     */
    public function getList($classid,$pn,$rn){
        return $this->dArticle->getList($classid,$pn,$rn);
    }

    /**
     * 根据ID获取信息
     */
    public function getById($classid)
    {
        return $this->dArticle->getById($classid);
    }
    /**
     * 根据ID获取总数
     */
    public function getCount($id)
    {
        return $this->dArticle->getCount($id);
    }
}
