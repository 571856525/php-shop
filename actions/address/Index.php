<?php

/**
 * 我的收货地址
 */

class Action_Index extends App_Action
{
    private $sUser;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sAddress = new Service_Address();
        $this->setView(Blue_Action::VIEW_SMARTY3);
        
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        //获取我的收货地址
        $list=$this->sAddress->getListByid($sess['id']);
        //$this->log($list);
        return array('list'=>$list);
    }

}
