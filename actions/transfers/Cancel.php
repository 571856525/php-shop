<?php

/**
 * 取消订单功能
 */

class Action_Cancel extends App_Action
{
    private $sTransfers;
    private $sUser;


    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->sTransfers = new Service_Transfers();
        $this->sCarts = new Service_Carts();
        $this->sWarehouse = new Service_Warehouse();
        $this->sUser = new Service_User();
        $this->sGoods= new Service_Goods();
        $this->setView(Blue_Action::VIEW_JSON);
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        $ret=$this->verify();
        $transfers=$this->sTransfers->getById($ret['id']);
        if($transfers['fromid']==$sess['id']){
            $this->Warning('不能取消下级订单');
        }
        Blue_Commit::call('transfers_Cancel', $ret);
        return $ret;

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
