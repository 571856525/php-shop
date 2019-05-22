<?php

/**
 * 提现
 */

class Action_Apply extends App_Action
{
    private $sApply;
    public function __prepare()
    {
        $this->hookNeedMsg = true;
        //$this->NeedLogin = true;
        $this->sApply=new Service_Apply();
        $this->sUser=new Service_User();
        $this->setView(Blue_Action::VIEW_JSON);
         
    }

    public function __execute()
    {
        $sess=$this->getSession();
        $user=$this->sUser->getById($sess['id']); 
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY3);
            $data['mobile']=$sess['mobile'];
            $data['real_name']=$sess['real_name'];
            return array('data'=>$data);
        } 
        else
        {
            $req=$this->verify();//判断余额
            if($req['amount']>$user['amount'])
            {
                $this->Warning('余额不足');
            }
            $apply=$this->sApply->getUnaudited($sess['id']);
            if(!empty($apply)){
                 $this->Warning('尚有未处理的提现申请，请耐心等待管理员审核');
            }
            $ret = array(
                'userid' => $sess['id'],
                'mobile' => trim($req['mobile']),
                'amount' => intval($req['amount']),
                'addtime' => time()
            );
            $data = array(
                'openid' => $sess['openid'],
                'mobile' => $req['mobile'],
                'real_name' => $req['real_name']
            );
            Blue_Commit::call('user_Update', $data);
            Blue_Commit::call('user_Apply', $ret);
            return $ret;
        }

    }
    public function verify()
    {
        $rule = array(
            'mobile' =>array('filterStrlen',array(1,256)),//手机号码
            'amount' =>array('filterStrlen',array(0,256)),//提现金额
            'real_name' =>array('filterStrlen',array(1,256)),//真实姓名
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        if (empty($req['mobile'])) {
            throw new Blue_Exception_Warning('手机号不能为空');
        }
        if (empty($req['real_name'])) {
            throw new Blue_Exception_Warning('姓名不能为空');
        }
        if (empty($req['amount'])) {
            throw new Blue_Exception_Warning('金额不能为空');
        }
        return $req;
    }

}
