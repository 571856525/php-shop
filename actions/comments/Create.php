<?php

/**
 * 用户对某个商品进行评论
 */

class Action_Create extends App_Action
{
    private $sCart;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sComments = new Service_Comments();
        $this->sCart = new Service_Cart();
    }

    public function __execute()
    {
        $session = $this->getSession();
        $req=$this->verify();
        $cart=$this->sCart->getByOrderId($req['id']);
        $ids=array();
        foreach ($cart as &$value) {
            array_push($ids, $value['goodsid']);
        }   
        $data=array(
            'ids'=>$ids,
            'orderid'=>$req['id'],
            'content'=>$req['content'],
            'img_url'=>$req['img_url'],
            'userid'=>$session['id'],
        );
        Blue_Commit::call('comments_Create', $data);
        return array('comments' => 1);
    }
    public function verify()
    {
        $rule = array(
            'id' => array('filterIntBetweenWithEqual', array(0)),
            'content' => array('filterStrlen', array(1, 100)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        $req['img_url']=$_POST['img_url'];
        return $req;
    }

}
