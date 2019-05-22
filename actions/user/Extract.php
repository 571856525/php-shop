<?php

/**
 * 我的提成
 */

class Action_Extract extends App_Action
{
    private $sUser;
    private $sSales;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sUser = new Service_User();
        $this->sInvite = new Service_Invite();
        $this->sReward = new Service_Reward();
        $this->sRecord = new Service_Record();
        $this->setView(Blue_Action::VIEW_SMARTY3);     
    }

    public function __execute()
    {
        $sess=$this->getSession();    
        //获取我的提成记录
        $list=$this->sRecord->getListByType(4,$sess['id']);
        foreach ($list as &$value) {
            if(!empty($value['subid'])){
                $user=$this->sUser->getById($value['subid']);
                $value['headimgurl']=$user['headimgurl'];
                $value['nickname']=$user['nickname'];
            }
            $value['addtime']=date('Y-m-d H:i:s', $value['addtime']);
        }
        return array('list'=>$list);
    }
}
