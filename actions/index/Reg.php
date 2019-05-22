<?php

/**
 * 注册
 *
 * @author xuefei@yunbix.com
 */

class Action_Reg extends App_Action
{ 
    private $sUser;
    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sUser = new Service_User();
    }

    public function __execute()
    {   
        $backUrl = !empty($_GET['backUrl']) ? trim($_GET['backUrl']) : '' ;
        $sess=$this->getSession(); 
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY3);
            return array('backUrl'=>$backUrl);
        } 
        else
        {
            //密码加密
            $_POST['password']=$this->sUser->password($_POST['password']);
            $req=$this->verify();
            //判断此用户是否在数据库中存在
            $userinfo = $this->sUser->getByname($req['username']);
            if($userinfo)
            {
                $this->Warning('用户名已存在');
            }
            
            $req['openid']=$sess['openid'];
            Blue_Commit::call('user_Update', $req);
            $userinfo = $this->sUser->getByname($req['username']);
            $this->setLogin($userinfo, 86400 * 30); //写入session
            return $req;
        }
    }
    public function verify()
    {
        $rule = array(
            'username' =>array('filterStrlen',array(1,256)),//用户名
            'password' =>array('filterStrlen',array(1,256)),//密码
            'real_name' =>array('filterStrlen',array(1,256)),//姓名
            'mobile' =>array('filterStrlen',array(1,256)),//手机号码
            'sex' =>array('filterStrlen',array(1,256)),//性别
            'address' =>array('filterStrlen',array(1,256))//地区
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }

}
