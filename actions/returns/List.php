<?php

/**
 * 我的售后列表
 */

class Action_List extends App_Action
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
        $list=$this->sReturns->getList($sess['id']);
        foreach ($list as &$value) {
        	$value['create_time']=date('Y-m-d H:i:s',$value['create_time']);
            $value['update_time']=date('Y-m-d H:i:s',$value['update_time']);
        	$order=$this->sOrder->getById($value['orderid']);
        	$cart=$this->sCart->getByOrderId($order['id']);
            foreach($cart as $k=>$v)
            {
                $cart[$k]['goods']=$this->sGoods->getInfo($v['goodsid']);
            }
            $value['order']=$order;
            $value['cart']=$cart;
        }
        return array('list'=>$list);
    }
}
