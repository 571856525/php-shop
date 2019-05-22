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
        $this->setView(Blue_Action::VIEW_SMARTY3);
        $this->sOrder = new Service_Order();
        $this->sCart = new Service_Cart();
        $this->sGoods = new Service_Goods();
        $this->sReward = new Service_Reward();
        $this->sAddress = new Service_Address();
        $this->sReturns= new Service_Returns();
    }

    public function __execute()
    {
        if ($this->getRequest()->isGet()) {

            $session = $this->getSession();
            $id = !empty($_GET['id']) ?  intval($_GET['id']) : 0;
            if(empty($id))
            {
                $this->Warning('参数不能为空');
            }
            //订单信息
            $info = $this->sOrder->getById($id);
            //订单商品
            $cart = $this->sCart->getByOrderId($info['id']);
            foreach($cart as &$v)
            {
                $v['goods']=$this->sGoods->getById($v['goodsid']);
                $v['goods']['amount']=$v['amount'];
            }
            //发货后为物流信息
            if($info['state']>=2){
                if($info['shipping']){
                    switch ($info['shipping']) {
                        case 1: //申通
                            $type='STO';
                            break;
                        case 2: //中通
                            $type='ZTO';
                            break;
                        case 3: //园通
                            $type='YTO';
                            break;
                        case 4:  //顺丰
                            $type='SFEXPRESS';
                            break;
                        case 5:  //韵达
                            $type='YUNDA';
                            break;   
                        case 6:  //百世快递
                            $type='HTKY';
                            break;
                        case 7:  //EMS
                            $type='EMS';
                            break;
                        case 8:  //德邦
                            $type='DEPPON';
                            break;
                    }
                    $logistics=new App_Logistics();

                    $express=$logistics->getShipping($info['shippingsn'],$type);
                    if($express){
                        //物流状态
                    $status=$express['result']['deliverystatus'];
                    //物流信息
                    $express=$express['result']['list'];
                    }
                }
            }
            $returns=$this->sReturns->getByOrder($id);
            //申请退款的条件
            if(empty($returns) || $returns['status']==2){
                $apply=1;
                if($info['state']!=0){
                   //付款，发货，确定收货7日内可以申请售后
                   $time=60*60*24*7;
                   $diff=$time-(time()-$info['update_time']);
                   if($diff<0){
                        unset($apply);
                   }
                }
            }
            //收货地址
            $address = $this->sAddress->getById($info['addressid']);
            $address['address']=$address['address'].$address['detail'];
            return array('data' => $info, 'cart' => $cart, 'address' => $address, 'express'=>$express,'status'=>$status,'comments'=>$info['comments'],'apply'=> $apply);
        }
        else
        {
            $this->setView(Blue_Action::VIEW_JSON);
            //确认收货
            $req= $this->verify();  
            if(empty($req['id']))
            {
                $this->Warning('参数不能为空');
            }
            Blue_Commit::call('order_Confirm', $req);
            return  $req;
        }
    }
    public function verify()
    {
        $rule = array(
            'id' => array('filterIntBetweenWithEqual', array(0))
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }
}
