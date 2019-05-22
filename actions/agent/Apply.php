<?php
/**
 * 代理商申请
 */
class Action_Apply extends App_Action
{
    
    public function __prepare(){
        $this->hookNeedMsg = true;
        $this->sReward =new Service_Reward();
        $this->sTrade =new Service_Trade();
        $this->sUser =new Service_User();
        $this->sAgent =new Service_Agent();
        $this->setView(Blue_Action::VIEW_JSON);
    }

    public function __execute()
    {
        $sess=$this->getSession();
        if($this->getRequest()->isGet()){
            $this->setView(Blue_Action::VIEW_SMARTY3);
            $level=$_GET['level'];
            $weixin = new App_Weixin();
            $sdk = $weixin->getSDK();
            return array('level'=>$level,'sess'=>$sess);
        }else {
            $req = $this->verify();
            $data = array(
                'level'     => $req['level'],
                'real_name' => $req['real_name'],
                'mobile'     => $req['mobile'], 
                'email'     => $req['email'], 
                'sex'     => $req['sex'], 
                'birth'     => $req['birth'], 
                'idno'     => $req['idno'], 
                'position'     => $req['position'], 
                'address'     => $req['address'], 
                'userid'    => $sess['id'],
                'create_time'=>time()
            );
            Blue_Commit::call('user_Update', array('openid'=>$sess['openid'],'real_name'=>$req['real_name'],'mobile'=>$req['mobile']));
            $user= $this->sUser->getById($sess['id']);
            if($user['reward']==$req['level']){
                 $this->Warning('你已经是该级别的分销商了');
            }
            if($user['reward']>$req['level']){
                 $this->Warning('不能申请比你级别低的代理商');
            }
            $agent=$this->sAgent->getAgentById($sess['id'],0);
            if($agent){
                $this->Warning('你已经申请了代理商，还未审核，请等待！！');
            }
            // if($req['level']<4){
            //     $tradesn = date('YmdHis') . rand(10000000, 99999999);
            //     $agentDate=array(
            //         'level'     => $req['level'],
            //         'real_name' => $req['real_name'],
            //         'mobile'    => $req['mobile'], 
            //         'userid'    =>$sess['id'],
            //         'tradesn'   =>$tradesn,
            //         'status'    => 0,
            //         'create_time'=>time()
            //     );
            //     Blue_Commit::call('agent_Create', $agentDate);
            //     //vip和分销
            //     $reward=$this->sReward->get($req['level']);
            //     if($req['level']==2){
            //         $amount=$reward['re_price']*$reward['re_goods'];
            //     }
            //     if($req['level']==3){
            //         $amount=$reward['re_loan']+$reward['re_margin'];
            //     }
            //     if($user['amount']>=$amount)
            //     {
            //         $real_amount=0;
            //     }
            //     else
            //     {
            //         $real_amount=$amount-$user['amount'];
            //     }
            //     $tradeDate=array(
            //         'tradesn'=>$tradesn,
            //         'userid' =>$sess['id'],
            //         'amount' =>$amount,
            //         'real_amount' =>$real_amount,
            //         'create_time'=>time(),
            //         'type'   =>  $req['level']-1
            //     );

            //     Blue_Commit::call('trade_Create', $tradeDate);
            //     //全余额支付
            //     if($real_amount == 0)
            //     {
            //         $trade = $this->sTrade->getByTrade($tradesn);  
            //         Blue_Commit::call('trade_Update', $trade);
            //         return array('money' =>0,'tradesn' =>  $tradesn);
            //         exit();
            //     }
            //     $real_amount=0.01;
            //     $pay = new App_Pay();
            //     $prepay_id = $pay->getPrepayId($sess['openid'], $tradesn, $real_amount*100, 2);
            //     core_log::debug('微信订单信息金额----代理商申请******'.json_encode($prepay_id));
            //     return array('tradeDate' => $tradeDate, 'prepay_id' => $prepay_id,'money'=>$real_amount*100);
            // }else{
            //     Blue_Commit::call('agent_Create', $data);
            //     return array();
            // } 
            Blue_Commit::call('agent_Create', $data);
            return array();
        }
    }

    public function __complete(){
    }

    public function verify(){
        $rule = array(
            'level' => array('filterIntBetweenWithEqual', array(1, 5)),
            'real_name' => array('filterStrlen', array(1, 10)),
            'mobile' => array('filterStrlen', array(1, 11)),
            'sex' => array('filterIntBetweenWithEqual', array(1, 2)),
            'birth' => array('filterStrlen', array(1, 20)),
            'idno' => array('filterStrlen', array(1, 18)),
            'email' => array('filterStrlen', array(1, 50)),
            'position' => array('filterStrlen', array(1, 50)),
            'address' => array('filterStrlen', array(1, 100)),
        );
        return Blue_Filter::filterArray($_POST, $rule);
    }
}