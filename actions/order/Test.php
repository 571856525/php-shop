<?php

/**
 * 测试
 */

class Action_Test extends App_Action
{

    private $sCarts;


    public function __prepare()
    {
        
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sCarts = new Service_Carts();

    }

    public function __execute()
    {

        $session = $this->getSession();
        

        return array('aa'=>'111');
        $lama = new App_Lama();
		//更新月度销量
        //获取订单下商品
        $carts =$this->sCarts->getByOrderId(31);
        foreach($carts as $v)
        {
            //更新库存及月度销量
            $lama->changeWare($v['userid'], $v['goodsid'], $v['num']);
        }
    }


}
