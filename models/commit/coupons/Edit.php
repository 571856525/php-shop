<?php
/**
 */
class Commit_Coupons_Edit extends Blue_Commit{
    private $dCoupons;
    protected function __register(){
        $this->transDB = array('shop');
    }
    protected function __prepare(){
        $this->dCoupons = new Dao_Coupons();
    }
    protected function __execute(){
        $ret = $this->getRequest();
        $id = $ret['id'];
        unset($ret['id']);
        $coupons = $this->dCoupons->selectOne(sprintf('id=%d',$id),'id,status');
        if($coupons['status']<2 && empty($coupons['receive_id'])){
            $this->dCoupons->update(sprintf('id=%d',$id),'status=1');
        }
    }
}

