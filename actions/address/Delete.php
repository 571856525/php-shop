<?php

/**
 * 删除收货地址
 */

class Action_Delete extends App_Action
{

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sAddress = new Service_Address();
        $this->setView(Blue_Action::VIEW_JSON);
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        $req=$this->verify();
        $ret = array(
            'id' => $req['id'],
            'status'=>0
        );
        Blue_Commit::call('address_Update', $ret);
        return $ret;
    }
    public function verify()
    {
        $rule = array(
            'id' =>array('filterIntBetweenWithEqual', array(0)),//ID
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }

}
