<?php

/**
 * 自动回复接口数据
 */

class Dao_Reply extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'reply');
    }

    /**
     * 根据类型获取
     */
    public function getByType($type)
    {
        if($type == 1){
            return $this->selectOne(sprintf('type=%d and status=0', $type), '*');
        }else{
            return $this->select(sprintf('type=%d and status=0', $type), '*');
        }    
    }

    /**
     * 根据关键字获取
     */
    public function getByKey($key)
    {
        return $this->selectOne(sprintf('type=2 and status=0 and `key`="%s"', $key), '*');      
    }



}

