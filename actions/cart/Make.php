<?php

/**
 * 加入/修改购物车
 */

class Action_Make extends App_Action
{
    private $sCart;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sCart = new Service_Cart();
    }

    public function __execute()
    {
        $req=$this->verify(); 
        $req['userid']=$session['id'];
        Blue_Commit::call('cart_Make', $req);
        return array('data' => $req);
    }
    public function verify()
    {
        return $_POST;
    }

}