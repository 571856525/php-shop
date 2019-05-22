<?php

/**
 * 取消收藏
 */

class Action_Delete extends App_Action
{
    private $sCart;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
    }

    public function __execute()
    {
        $session = $this->getSession();
        $req=$this->verify();
        Blue_Commit::call('collection_Delete', $req);
        return array('status' => 1);
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