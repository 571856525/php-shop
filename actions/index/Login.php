<?php

/**
 * 登录
 *
 * @author xuefei@yunbix.com
 */

class Action_Login extends App_Action
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
        //var_dump($_GET);die;
        $sess=$this->getSession(); 
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY3);

            //物流信息接口
            // $logistics= new App_Logistics();
            // $shipping=$logistics->getShipping('801362607001933503','YTO');
            // $this->log($shipping);
            return array('backUrl'=>$backUrl);
        } 
        else
        {
            $req=$this->verify();
            //判断此用户是否在数据库中存在
            $userinfo = $this->sUser->getByname($req['username']);
            if(empty($userinfo))
            {
                $this->Warning('用户不存在');
            }
            if($userinfo['password']!=$this->sUser->password($req['password']))
            {   
                $this->Warning('密码错误');  
            }
            //用户名和密码相匹配的openid是否和session中的openid相同
            $openid= $this->sUser->getOpenidByname($req['username'],$this->sUser->password($req['password']));
            if($openid!=$sess['openid']){
               $this->Warning('用户的openid不匹配');  
            }
            $this->setLogin($userinfo, 86400 * 30); //写入session
            return $req;
        }
    }
    public function verify()
    {
        $rule = array(
            'username' =>array('filterStrlen',array(1,256)),//用户名
            'password' =>array('filterStrlen',array(1,256)),//密码
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }

}
