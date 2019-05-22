<?php

/**
 * 退货详情
 */

class Action_Info extends App_Action
{

    private $sReturns;
    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_SMARTY3);
        $this->sOrder = new Service_Order();
        $this->sReturns = new Service_Returns();
        $this->sCart= new Service_Cart();
        $this->sGoods= new Service_Goods();
    }
    public function __execute()
    {
        $sess=$this->getSession();
        $id=!empty($_GET['id'])?$_GET['id']:0;
        if(empty($id)){
            $this->Warning("id不能为空");
        }
        $info=$this->sReturns->getById($id);
        $info['create_time']=date('Y-m-d H:i:s',$info['create_time']);
        $info['update_time']=date('Y-m-d H:i:s',$info['update_time']);
        $order=$this->sOrder->getById($info['orderid']);
        $cart=$this->sCart->getByOrderId($order['id']);
        foreach($cart as $k=>$v)
        {
            $cart[$k]['goods']=$this->sGoods->getInfo($v['goodsid']);
        }
        $info['order']=$order;
        $info['cart']=$cart;
        return array('data'=>$info);
    }
}
