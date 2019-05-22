<?php
/**
 * 优惠券相关相关
 */
class Service_Coupons
{
    private $dCoupons;
    public function __construct(){
        $this->dCoupons = new Dao_Coupons();
    }
    /*
     * id 获取单条
     */
    public function get($id){
        return $this->dCoupons->get($id);
    }
    /*
     * 获取可用优惠券
     */
    public function getByPay($openid){
        return $this->dCoupons->getByPay($openid);
    }
    //优惠券列表
    public function getList($type){
        return $this->dCoupons->getList($type);
    }

    //是否领取
    public function IfHaving($userid,$logo){
        return $this->dCoupons->IfHaving($userid,$logo);
    }

    //优惠券列表
    public function getAllList($userid){
        return $this->dCoupons->getAllList($userid);
    }

    public function IfExist($logo){
        return $this->dCoupons->IfExist($logo);
    }

}

