<?php

/**
 * 某一个用户的订单详情
 */

class Action_Lists extends App_Action
{

    private $sOrder;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_SMARTY3);
        $this->sOrder = new Service_Order();
    }

    public function __execute()
    {
        if ($this->getRequest()->isGet()) {
            $session = $this->getSession();
            $userid = !empty($_GET['userid']) ?  intval($_GET['userid']) : 0;
            if(empty($userid))
            {
                $this->Warning('参数不能为空');
            }
            //订单信息
            $order = $this->sOrder->getByUserId($userid);
            foreach($order as &$v)
            {
                $v['addtime']=date('Y-m-d H:i:s',$v['addtime']);
            }
            return array('list' => $order);
        }
    }
}
