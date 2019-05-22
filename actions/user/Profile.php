<?php

/**
 * 修改个人资料/绑定手机
 */

class Action_Profile extends App_Action
{
    private $sUser;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sUser = new Service_User();
        $this->setView(Blue_Action::VIEW_JSON);
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY3);
            $user = $this->sUser->getById($sess['id']);
            return array('data'=>$user);
        } 
        else
        {
            $req=$this->verify();
            $ret = array(
                'openid' => $sess['openid'],
                'real_name' => $req['real_name'],
                'mobile' => $req['mobile'],
                'sex' => $req['sex'],
                'address' => $req['address']
            );
            Blue_Commit::call('user_Update', $ret);
            return $ret;
        }
    }
    public function verify()
    {
        $rule = array(
            'real_name' =>array('filterStrlen',array(1,256)),//姓名
            'mobile' =>array('filterStrlen',array(1,256)),//手机号码
            'sex' =>array('filterStrlen',array(1,256)),//性别
            'address' =>array('filterStrlen',array(1,256))//地区
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        if (empty($req['mobile'])) {
            throw new Blue_Exception_Warning('手机号不能为空');
        }
        return $req;
    }

}
