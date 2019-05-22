<?php

/**
 * 购物车列表
 */

class Action_Index extends App_Action
{
    private $sCart;
    private $sGoods;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sCart = new Service_Cart();
        $this->sGoods = new Service_Goods();
        $this->sReward = new Service_Reward();
        $this->sUser = new Service_User();
        $this->sGoodstype = new Service_Goodstype();
        $this->sPromotion= new Service_Promotion();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $session = $this->getSession();
        $cart=$this->sCart->getByUserId($session['id']);
        $user=$this->sUser->getById($session['id']);
        foreach($cart as $k=>$v)
        {
            $cart[$k]['goods']=$this->sGoods->getById($v['goodsid']);
            //促销商品
            $promotion=$this->sPromotion->getIfOne($v['goodsid']);
            if(!empty($promotion)){
                $data['start_time']=date('Y-m-d H:i:s',$promotion['start_time']);
                $data['end_time']=date('Y-m-d H:i:s',$promotion['end_time']);
                //秒杀
                if($promotion['type']==1){
                    if($promotion['num']>0){
                        $cart[$k]['pro_state'] =2;
                        $cart[$k]['pro_amount']=$promotion['amount'];
                        $cart[$k]['number']=$promotion['num'];
                        $cart[$k]['sum']=$promotion['sum'];
                    }else{
                        unset($promotion);
                    }
                }
                //多买省
                if($promotion['type']==2){
                    $cart[$k]['pro_state'] =3;
                    $cart[$k]['number']=$promotion['num'];
                    $cart[$k]['save']=$promotion['save'];
                   
                }
                //多买送
                if($promotion['type']==3){
                    $cart[$k]['pro_state'] =4;
                    $cart[$k]['send_gid']=$promotion['send_gid'];
                    $cart[$k]['send_amount']=$promotion['send_amount'];
                }
            }
        }
        return array('cart' => $cart);
    }
}
