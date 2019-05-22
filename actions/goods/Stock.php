<?php

/**
 * 微信支付失败回调
 */

class Action_Stock extends App_Action
{
    private $sGoods;
    private $sCart;
    private $sCarts;
    private $sOrder;
    private $sTransfers;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sGoods = new Service_Goods();
        $this->sCart = new Service_Cart();
        $this->sCarts = new Service_Carts();
        $this->sOrder = new Service_Order();
        $this->sTransfers = new Service_Transfers();
        
    }

    public function __execute()
    {
        $type =!empty($_POST['type']) ? trim($_POST['type']) : 'order';
        $id =!empty($_POST['id']) ? trim($_POST['id']) : '';
        //订单库存
        if($type =='order')
        {
            core_log::debug('库存问题');
            Blue_Commit::call('order_Failure', array('ordersn'=>$id));
        }else if($type =='auperior'){
            core_log::debug('上级库存问题');
            Blue_Commit::call('order_Afailure', array('ordersn'=>$id));
        }
        //云仓库存
        else
        {
            core_log::debug('上级云仓库存问题');
            Blue_Commit::call('transfers_Failure', array('ordersn'=>$id));
        }
        return array('type' => $type);
    }
}
