<?php

/**
 * 订单列表
 */

class Action_Index extends App_Action
{
    private $sOrder;
    private $sCart;
    private $sGoods;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sOrder = new Service_Order();
        $this->sCart = new Service_Cart();
        $this->sCarts = new Service_Carts();
        $this->sGoods = new Service_Goods();
        $this->sTransfers = new Service_Transfers();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $session = $this->getSession();
        if($this->getRequest()->isGet()){
            $rn =!empty($_GET['rn']) ? intval($_GET['rn']) : 10;
            $pn =!empty($_GET['pn']) ? intval($_GET['pn']) : 1;
            $state=isset($_GET['state'])? $_GET['state'] : NULL;
             //是否为云仓订单
            if(!empty($_GET['transfers'])){
                $list = $this->sTransfers->getOrderByUserId($session['id'],$state);
                
            }else{
                //商城订单
                $list = $this->sOrder->getOrders($session['id'],$pn,$rn,$state);
            }
            foreach($list as &$data)
            {
                if(!empty($_GET['transfers'])){
                        $cart = $this->sCarts->getByOrderId($data['id']);
                }else{
                        $cart = $this->sCart->getByOrderId($data['id']);
                }
                foreach($cart as &$v)
                {

                    $v['goods']=$this->sGoods->getInfo($v['goodsid']);
                }
                $data['cart']=$cart;
            }
            return array('list' => $list);
        }
        $ret=$this->vertify();
        $pn=$ret['pn'];
        $state=isset($ret['state'])? $ret['state'] : NULL;
         //是否为云仓订单
        if(!empty($_GET['transfers'])){
            $list = $this->sTransfers->getOrderByUserId($session['id'],$state);
            
        }else{
            //商城订单
            $list = $this->sOrder->getOrders($session['id'],$pn,$rn,$state);
        }
        foreach($list as &$data)
        {
            if(!empty($_GET['transfers'])){
                    $cart = $this->sCarts->getByOrderId($data['id']);
            }else{
                    $cart = $this->sCart->getByOrderId($data['id']);
            }
            foreach($cart as &$v)
            {
                $v['goods']=$this->sGoods->getInfo($v['goodsid']);
            }
            $data['cart']=$cart;
        }
        return array('list' => $list);
        
    }
    public function vertify(){
        $rule = array(
            'pn' => array('filterIntBetweenWithEqual', array(0)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        $req['state']=$_POST['state'];
        return $req;
    }
}
