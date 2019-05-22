<?php

//促销商品
class Service_Promotion
{
    private $dPromotion;

    public function __construct()
    {
        $this->dPromotion = new Dao_Promotion();
    }

    /**
     * 根据金额获取利率
     */
    public function getlist($type,$pn,$rn)
    {
        return $this->dPromotion->getlist($type,$pn,$rn);
    }

    /* 根据金额获取利率
    */
   public function getCount($type)
   {
       return $this->dPromotion->getCount($type);
   }
    
    /**
     * 
     */
    public function get($id)
    {
        return $this->dPromotion->get($id);
    }

    
    public function getById($id)
    {
        return $this->dPromotion->getById($id);
    }

     /**
     * 
     */
    public function getOne($type, $goodsid, $status='')
    {
        return $this->dPromotion->getOne($type, $goodsid, $status);
    }


    public function getIfOne($goodsid)
    {
        return $this->dPromotion->getIfOne($goodsid);
    }



    public function getProOne($goodsid,$status='')
    {
        return $this->dPromotion->getProOne($goodsid,$status);
    }
    
    

}
