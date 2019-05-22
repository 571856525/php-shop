<?php
/**
 * 代理商申请
 */
class Action_List extends App_Action
{
    
    public function __prepare(){
        $this->hookNeedMsg = true;
        $this->sReward =new Service_Reward();
        $this->sUser =new Service_User();
        $this->sAgent =new Service_Agent();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $sess=$this->getSession();
        $list=$this->sAgent->getList($sess['id']);
        foreach ($list as &$value) {
            $value['create_time']=date('Y-m-d H:i:s',$value['create_time']);
            $value['level']=$this->sReward->get($value['level'])['typename'];
        }
        $this->log(array('list'=>$list));
    }
}