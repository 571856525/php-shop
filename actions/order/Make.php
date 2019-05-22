<?php

/**
 * 购物车购买方式
 */

class Action_Make extends App_Action
{

    private $sGoods;
    private $sCart;
    private $sOrder;
    private $sAddress;
    private $sUser;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sGoods = new Service_Goods();
        $this->sCart = new Service_Cart();
        $this->sOrder = new Service_Order();
        $this->sAddress = new Service_Address();
        $this->sUser = new Service_User();
        $this->sReward = new Service_Reward();
        $this->sGoodstype = new Service_Goodstype();
        $this->sCoupons = new Service_Coupons();
        $this->sPromotion= new Service_Promotion();
        $this->sCombination= new Service_Combination();
    }

    public function __execute()
    {
        $session = $this->getSession();
        $user= $this->sUser->getById($session['id']);
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY3);
            $id = !empty($_GET['id']) ? trim($_GET['id']) : '';
            $user=$this->sUser->getById($session['id']);
            if(empty($id))
            {
                $this->Warning('组合商品不能为空');
            }
            $comList=$this->sCombination->get($id);
            if($comList){
                if($comList['goods_id1']){
                    $comList['goods1']=$this->sGoods->getInfo($comList['goods_id1']);
                }
                if($comList['goods_id2']){
                    $comList['goods2']=$this->sGoods->getInfo($comList['goods_id2']);
                }
                if($comList['goods_id3']){
                    $comList['goods3']=$this->sGoods->getInfo($comList['goods_id3']);
                }
                if($comList['goods_id4']){
                    $comList['goods4']=$this->sGoods->getInfo($comList['goods_id4']);
                }
                if($comList['goods_id5']){
                    $comList['goods5']=$this->sGoods->getInfo($comList['goods_id5']);
                }
            }


            $list=$this->sAddress->getListByid($session['id']);
            foreach($list as &$value){
                $value['address']=$value['address'].$value['detail'];
            }


            $weixin = new App_Weixin();
            $sdk = $weixin->getSDK();
            // $this->log(array('comList' => $comList,'sdk' => $sdk,'list' => $list));
            return array('comList' => $comList,'sdk' => $sdk,'list' => $list);
        }
    }
}
