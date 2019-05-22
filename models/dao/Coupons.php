<?php
/**
 * 优惠券
 */
class Dao_Coupons extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'coupons');
    }
    /*
     * id 获取单条
     */
    public function get($id){
        return $this->selectOne(sprintf('id=%d',$id),'*');
    }
    /*
     * 优惠券列表
     */
    public function getList($goodsid=''){
        if(empty($goodsid)){
            $ret = $this->select("status in (0,1) and goods_id=0 and end_time>".time(), '*','group by logo order by money asc,full desc');
        }else{
            $ret = $this->select("status in (0,1) and end_time>".time()." and (goods_id=0 or goods_id=".$goodsid.")", '*','group by logo order by money asc,full desc');
        }
        return $ret;
    }


    //是否领取
    public function IfHaving($userid,$logo){
        $ret = $this->selectOne("logo='".$logo."' and userid=".$userid, '*');
        return $ret;
    }

    //是否领完
    public function IfExist($logo){
        $ret = $this->selectOne("logo='".$logo."'  and status=0 ", 'id');
        return $ret;
    }

    //优惠券列表
    public function getAllList($userid){
        $ret = $this->select("status in (1,2,9) and end_time>".time()." and userid=".$userid, '*','order by money asc,full desc');
        return $ret;
    }

    //根据openid获取优惠券
    public function getByPay($userid){
        $ret = $this->select("status in (1,2) and end_time>".time()." and userid=".$userid, '*','order by money asc,full desc');
        return $ret;
    }
}
