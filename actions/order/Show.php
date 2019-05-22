<?php

/**
 * 代理商申请
 */

class Action_Show extends App_Action
{
    private $sArticle;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sArticle = new Service_Article();
        $this->sUser = new Service_User();
        $this->sAgent = new Service_Agent();
        $this->sReward = new Service_Reward();
        $this->sProfession = new Service_Profession();
        $this->setView(Blue_Action::VIEW_JSON);
    }

    public function __execute()
    {
        $session = $this->getSession();
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY2);
            $user = $this->sUser->getById($session['id']);
            $id =!empty($_GET['id']) ? intval($_GET['id']) : 0;
            if(empty($id))
            {
                $this->Warning('ID不能为空');
            }
            $agent=$this->sAgent->getAgentById($session['id'],0);//未审核
            $agent2=$this->sAgent->getAgentById($session['id'],2);//已审核
            if($user['reward']==1 && empty($agent)){
                //立即加入代理商
                $type=1;
            }
            if($user['reward']>1 && $user['reward']<5){
                //升级
                $type=3;
            }
            if(!empty($agent)){
                //已申请，未审核
                $type=2;
            }
            if($user['reward']==5){
                //你已经是最高级别的代理商了
                $type=4;
            }
            $data = $this->sArticle->getById($id);
            $weixin = new App_Weixin();
            $sdk = $weixin->getSDK();
            $profession=$this->sProfession->getList();

            $rewardList=$this->sReward->getOnelist();
            
            $reward = $this->sReward->getOne($user['reward']);
            return array('reward'=>$rewardList, 'typename'=>$reward['typename'],'data' => $data,'profession'=>$profession,'user' => $user,'type' => $type,'sdk'=>$sdk);
        }    
    }
}
