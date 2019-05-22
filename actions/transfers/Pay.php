<?php

/**
 * 未支付进行支付的
 */

class Action_Pay extends App_Action
{
    private $sUser;
    private $sTransfers;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->sUser = new Service_User();
        $this->sTransfers = new Service_Transfers();
        $this->setView(Blue_Action::VIEW_JSON);
    }

    public function __execute()
    {
            $sess=$this->getSession(); 
            $req = $this->verify();
            $amount= $req['money'];
            //判断余额
            $user= $this->sUser->getById($sess['id']);
            //会员折扣


            if($user['amount']>=$amount)
            {
                $real_amount=0;
            }
            else
            {
                $real_amount=$amount-$user['amount'];
            }
            $transfersData=$this->sTransfers->getById($req['id']);
            $ordersn=$transfersData['ordersn'];

            //全余额支付
            if($user['amount']>=$amount)
            {
                $transfers = $this->sTransfers->getBySn($ordersn);    
                Blue_Commit::call('transfers_Update', $transfers);
                return array('money' => 0,'ordersn' => $ordersn);
                exit();
            }

            //统一下单调起支付
            $pay = new App_Pay();
            core_log::debug('微信订单信息金额----调拨申请******'.$money);

            $prepay_id = $pay->getPrepayId($sess['openid'], $ordersn, $real_amount*100, 2);
            core_log::debug('微信订单信息金额----调拨申请******'.json_encode($prepay_id));
            return array('orderData' => $orderData, 'prepay_id' => $prepay_id,'money'=>$real_amount*100); 
    }
    public function verify()
    {
        return $_POST;
    }

}
