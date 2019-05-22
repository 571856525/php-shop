<?php

/**
 * 充值
 */

class Action_Recharge extends App_Action
{
    private  $sSales;
    private  $sRecord;
    private  $sUser;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sTrade = new Service_Trade();
        $this->sRecord = new Service_Record();
        $this->sUser = new Service_User();
        $this->setView(Blue_Action::VIEW_JSON);   
    }
    

    public function __execute()
    {
        if($this->getRequest()->isGet()){
            $sess=$this->getSession();
            $this->setView(Blue_Action::VIEW_SMARTY3);   
            $list=$this->sTrade->getList($sess['id']);
            foreach ($list as &$value) {
                $value['create_time']=date('Y-m-d H:i:s',$value['create_time']);
            }
            return array('list'=>$list);
        }
        $req =$this->verify();
        $sess=$this->getSession();
        if($req['amount']){
            $tradesn = time() . rand(10000000,99999999);
            $data=array(
                'amount'    => $req['amount'], 
                'userid'    =>$sess['id'],
                'tradesn'   =>$tradesn,
                'status'    => 0,
                'create_time'=>time()
            );
            Blue_Commit::call('trade_Create', $data);
            $pay = new App_Pay();
            $prepay_id = $pay->getPrepayId($sess['openid'], $tradesn, 0.01*100, 3);
            core_log::debug('微信充值订单信息金额******'.json_encode($prepay_id));
            return array('tradeDate' => $data, 'prepay_id' => $prepay_id,'money'=>$req['amount']*100);
        }
    }

    public function verify()
    {
        $rule = array(
            'amount' =>array('filterStrlen',array(0,256)),//提现金额
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }
}
