<?php

/**
 * 我的发货列表列表
 */

class Action_Info extends App_Action
{
    private $sDelivery;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sDelivery = new Service_Delivery();
        $this->sUser= new Service_User();
        $this->sGoods= new Service_Goods();
        $this->sAddress= new Service_Address();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $sess=$this->getSession();
        if ($this->getRequest()->isGet()) {
            $id=$_GET['id'];
            if(empty($id)){
                $this->Warning('id不能为空');
            }
            $delivery=$this->sDelivery->getById($id);
            $delivery['addtime']=date('Y-m-d H:i:s',$delivery['addtime']);
            $commodity=json_decode($delivery['goods']);
            foreach($commodity as &$v){
                $v=get_object_vars($v);
                $goods=$this->sGoods->getInfo($v['id']);            
                $v['goodsname']=$goods['goodsname'];
                $v['goodspic']=$goods['goodspic'];  
            }
            $delivery['goods']=$commodity;
            $delivery['address']=$this->sAddress->getById($delivery['addressid']);
            //发货后为物流信息
            if($delivery['audit']=1){
                if($delivery['shipping']){
                    switch ($delivery['shipping']) {
                        case 1: //申通
                            $type='STO';
                            break;
                        case 2: //中通
                            $type='ZTO';
                            break;
                        case 3:   //园通
                            $type='YTO';
                            break;
                        case 4:  //顺丰
                            $type='SFEXPRESS';
                            break;
                        case 5:  //韵达
                            $type='YUNDA';
                            break; 
                        case 6:  //百世快递
                            $type='HTKY';
                            break;
                        case 7:  //EMS
                            $type='EMS';
                            break;
                        case 8:  //德邦
                            $type='DEPPON';
                            break;
                    }
                    $logistics=new App_Logistics();
                    $express=$logistics->getShipping($delivery['shippingsn'],$type);


                    if($express){
                        //物流状态
                        $status=$express['result']['deliverystatus'];
                        //物流信息
                        $express=$express['result']['list'];
                    }
                }
            }
            return array('data' => $delivery,'express'=>$express);
        }
    }
}