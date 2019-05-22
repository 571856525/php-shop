<?php

/**
 * 加入到我的收藏
 */

class Action_Create extends App_Action
{
    private $sCart;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sCollection= new Service_Collection();
        $this->sGoods= new Service_Goods();
    }

    public function __execute()
    {
        $session = $this->getSession();
        $req=$this->verify();
        $collection=$this->sCollection->getOne($session['id'],$req['id']);
        if(!empty($collection)){
            $this->Warning("你已经收藏过该商品了");
        }
        $data=array(
           'goodsid'=>$req['id'],
           'userid'=>$session['id'],
           'create_time'=>time(),
           'status'   => 1
        );
        Blue_Commit::call('collection_Create', $data);
        $goods=$this->sGoods->getById($req['id']);
        return array('amount' => $goods['amount']);
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
