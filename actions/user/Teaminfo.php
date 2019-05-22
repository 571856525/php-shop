<?php

/**
 * 团队信息
 */

class Action_Teaminfo extends App_Action
{

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sUser = new Service_User();
        $this->sOrder = new Service_Order();
        $this->sTransfers = new Service_Transfers();
        $this->setView(Blue_Action::VIEW_SMARTY3);
         
    }

    public function __execute()
    {
       if(!empty($_GET['id'])){
          $id=$_GET['id'];
       }
       $user=$this->sUser->getById($id);
       
       //本月订单总额
       $order= $this->sOrder->getMonthByid($user['id']) ;
       //本月云仓总额
       $transfers= $this->sTransfers->getMonthByid($user['id']) ;
       //本月业绩
       $user['order']=$order['amount'];
       $user['transfers']=$transfers['amount'];
       $user['month_amount']=$order['amount']+$transfers['amount'];
       return array('data'=>$user);
    }
}
