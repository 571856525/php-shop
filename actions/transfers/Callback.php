<?php

/**
 * 调拨购买结果
 */

class Action_Callback extends App_Action
{

    private $sTransfers;


    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->setView(Blue_Action::VIEW_SMARTY3);
        $this->sTransfers = new Service_Transfers();
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
            $transfers = $this->sTransfers->getBySn($ordersn);
            return array('order' => $transfers);  
        }
    }
}
