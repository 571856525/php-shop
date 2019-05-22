<?php

/**
 * 更新购物车
 */

class Action_Update extends App_Action
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
        
        $session = $this->getSession();
        $req=$this->verify();
        if(!empty($req))
        {
            $res['userid']=$session['id'];
            $res['goods']=json_encode($req);
            Blue_Commit::call('cart_Update', $res);
            return array('data' => $res);
        }
    }
    public function verify()
    {
        $req = $_POST;
        return $req;
    }

}
