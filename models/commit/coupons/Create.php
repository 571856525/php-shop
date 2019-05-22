<?php
/**
 * 赠送状态
 * @author name GouHui
 * @copyright 2015~ (c) @yunbix.com
 * @Time:  2018 11:55:06 AM CST
 */
class Commit_Coupons_Create extends Blue_Commit{
    private $dCoupons;
    protected function __register(){
        $this->transDB = array('shop');
    }
    protected function __prepare(){
        $this->dCoupons = new Dao_Coupons();
    }
    protected function __execute(){
        $ret = $this->getRequest();
        $coupons = $this->dCoupons->selectOne(sprintf("status=0 and logo='%s'",$ret['logo']),'id');
        if($coupons){
            $this->dCoupons->update(sprintf('id=%d',$coupons['id']),array('userid'=>$ret['userid'],'status'=>1));
        }
    }
}

