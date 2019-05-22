<?php

/**
 * 提现记录表
 */

class Action_Cashlist extends App_Action
{
    private $sUser;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sApply = new Service_Apply();
        $this->setView(Blue_Action::VIEW_SMARTY3);     
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        $data= $this->sApply->getList($sess['id']);
        foreach ($data as &$value) {
            $value['addtime']=date('Y-m-d h:i:s',$value['addtime']);
        }
        $weixin=new App_Weixin();
        $sdk=$weixin->getSDK();
        return array('list'=>$data,'sess'=>$sess,'sdk'=>$sdk);
    }
}