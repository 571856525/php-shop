<?php

/**
 * 订单详情
 */

class Action_Info extends App_Action
{

    private $sOrder;
    private $sCart;
    private $sGoods;
    private $sAddress;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->setView(Blue_Action::VIEW_SMARTY3);
        $this->sRecord = new Service_Record();
        $this->sOrder = new Service_Order();
        $this->sTransfers = new Service_Transfers();
        $this->sCart = new Service_Cart();
        $this->sCarts = new Service_Carts();
        $this->sGoods = new Service_Goods();
        $this->sAddress = new Service_Address();
    }

    public function __execute()
    {
        if ($this->getRequest()->isGet()) {
            $session = $this->getSession();
            $id = !empty($_GET['id']) ? trim($_GET['id']) : 0;
            $id = !empty($_GET['id']) ?  intval($_GET['id']) : 0;
            if(empty($id))
            {
                $this->Warning('参数不能为空');
            }
            $record = $this->sRecord->getById($id);
            if($record['type']==1){       //直购的
                //订单信息
                $info = $this->sOrder->getByOrder($record['ordersn']);
                //购物车信息
                $cart = $this->sCart->getByOrderId($info['id']);
            }else{  //调拨的
                //订单信息

                $info = $this->sTransfers->getBySn($record['ordersn']);
                //购物车信息
                $cart = $this->sCarts->getByOrderId($info['id']);
            }
            //商品信息
            foreach($cart as &$v)
            {
                $v['goods']=$this->sGoods->getById($v['goodsid']);
            }
            //收货地址
            $address = $this->sAddress->getById($info['addressid']);
            $this->log(array('data' => $info, 'cart' => $cart, 'address' => $address));
        }
    }
}
