<?php

class Service_Reward
{
    private $dReward;

    public function __construct()
    {
        $this->dReward = new Dao_Reward();
    }

    /**
     * 列表
     */
    public function getlist()
    {
        return $this->dReward->getlist();
    }
    
    /**
     * 根据id获取
     */
    public function get($id)
    {
        return $this->dReward->get($id);
    }


    public function getOnelist()
    {
        return $this->dReward->getOnelist();
    }

    public function getOne($id)
    {
        return $this->dReward->getOne($id);
    }



}
