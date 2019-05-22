<?php

/**
 * 删除购物车
 */

class Action_Delete extends App_Action
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
        $cart=$this->sCart->getById($req['id']);
        if(empty($cart)){
           $this->Warning('id不存在！！');
        }
        Blue_Commit::call('cart_Delete', $req);
        return array('data' => $req);
    }
    public function verify()
    {
        $rule = array(
            'id' => array('filterIntBetweenWithEqual', array(0)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }
}
