<?php

/**
 * 团队业绩和我的提成
 */

class Action_Downlist extends App_Action
{
    private $sUser;
    private $sSales;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sUser = new Service_User();
        $this->sInvite = new Service_Invite();
        $this->sReward = new Service_Reward();
        $this->sSales = new Service_Sales();
        $this->sOrder = new Service_Order();
        $this->sTransfers = new Service_Transfers();
        $this->sRecord = new Service_Record();
        $this->sReward = new Service_Reward();
        $this->setView(Blue_Action::VIEW_JSON);
        $this->setView(Blue_Action::VIEW_SMARTY3);     
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        //默认当月团队业绩业绩和团队提成
        $date=!empty($_GET['date'])?$_GET['date']:date('Y-m-d',time());
        if(empty($_GET['date'])){
            $month=intval(date('m',time()));
            $year=date('Y',time());
        }else{
            $month=substr($_GET['date'],5,2);  
            $year=substr($_GET['date'],0,4);
        }
        if(empty($_GET['type'])){
            $user=$this->sUser->getById($sess['id']);
            $sales=$this->sSales->getByIdMonth($sess['id'], $month,$year);
            $teamAmount=!empty($sales['money'])?$sales['money']:0; 
            $user['amount']=$teamAmount;
             //个人
            $user['my_money']=!empty($sales['my_money'])?$sales['my_money']:0;
            $user['level']=$this->sReward->get($user['reward'])['typename'];
            //获取我的下级列表
            $list=$this->sUser->getDown($sess['id']);
            foreach($list as &$data)
            {
                $sales=$this->sSales->getByIdMonth($data['id'], $month,$year);
                //团队
                $data['amount']=!empty($sales['money'])?$sales['money']:0;
                //个人
                $data['my_money']=!empty($sales['my_money'])?$sales['my_money']:0;
                $data['level']=$this->sReward->get($data['reward'])['typename'];
            }
            array_unshift($list,$user);
            // $this->log(array('list'=>$list,'teamAmount'=>$teamAmount));
            return array('list'=>$list,'teamAmount'=>$teamAmount);
        }else{

             //我的提成总和
             $user=$this->sUser->getById($sess['id']);
             $sum=$this->sRecord->getSumByType(4,$sess['id'],$year,$month);
             $user['sum']=!empty($sum)?$sum:0;
             $user['level']=$this->sReward->get($user['reward'])['typename'];
             //获取我的下级列表
             $list=$this->sUser->getDown($sess['id']);
             foreach($list as &$data)
             {
                $sum=$this->sRecord->getSumByType(4,$data['id'],$year,$month);
                $data['sum']=!empty($sum)?$sum:0;
                $data['level']=$this->sReward->get($data['reward'])['typename'];
             }
             array_unshift($list,$user);
             return array('list'=>$list);

             
            //我的提成列表
            // $list=$this->sRecord->getListByType(4,$sess['id'],$year,$month);
            // foreach ($list as &$value) {
            //     if(!empty($value['subid'])){
            //         $user=$this->sUser->getById($value['subid']);
            //         $value['headimgurl']=$user['headimgurl'];
            //         $value['nickname']=$user['nickname'];
            //     }
            //     $value['addtime']=date('Y-m-d H:i:s', $value['addtime']);
            // }
            // return array('list'=>$list);
        }   
    }
}
