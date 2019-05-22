<?php
/**
 * 代理商申请
 */
class Action_Info extends App_Action
{
    
    public function __prepare(){
        $this->hookNeedMsg = true;
        $this->sReward =new Service_Reward();
        $this->sUser =new Service_User();
        $this->sAgent =new Service_Agent();
        $this->sProfession =new Service_Profession();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $sess=$this->getSession();
        $data=$this->sAgent->getOneById($sess['id']);
        $data['create_time']=date('Y-m-d H:i:s',$data['create_time']);
        $data['level']=$this->sReward->get($data['level'])['typename'];
        $data['position']=$this->sProfession->get($data['position'])['name'];
        if($data['sex']==1){
            $data['sex']='男';   
        }else{
            $data['sex']='女';   
        }
        if($data['state']==0){
            $data['state']='正在审核';   
        }else{
            $data['state']='审核通过'; 
        }
        return array('data'=>$data);
    }
}