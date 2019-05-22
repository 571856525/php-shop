<?php

//职业选项表
class Service_Profession
{
    private $dProfession;

    public function __construct()
    {
        $this->dProfession = new Dao_Profession();
    }

    /**
     * 根据金额获取利率
     */
    public function getlist()
    {
        return $this->dProfession->getlist();
    }
    

    
    /**
     * 
     */
    public function get($id)
    {
        return $this->dProfession->get($id);
    }


}
