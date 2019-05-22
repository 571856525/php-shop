<?php

/**
 * 购买结果
 */

class Action_Callback extends App_Action
{

    private $sOrder;


    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->setView(Blue_Action::VIEW_SMARTY3);
        $this->sOrder = new Service_Order();
    }

    public function __execute()
    {
        if ($this->getRequest()->isGet()) {
            $session = $this->getSession();
            $ordersn = !empty($_GET['ordersn']) ? trim($_GET['ordersn']) : '';
            if(empty($ordersn))
            {
                $this->Warning('ordersn不能为空');
            }
            $order = $this->sOrder->getByOrder($ordersn);

            return array('order' => $order);  
        }
    }
}
