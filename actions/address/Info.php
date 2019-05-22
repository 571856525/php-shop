<?php

/**
 * 我的收货地址详细
 */

class Action_Info extends App_Action
{
    private $sUser;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sAddress = new Service_Address();
        $this->setView(Blue_Action::VIEW_JSON);
        
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        $id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
        //获取我的收货地址
        $info=$this->sAddress->getById($sess['id']);
        //$this->log($info);
        return array('info'=>$info);
    }
}
