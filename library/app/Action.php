<?php

class App_Action extends Blue_Action
{

    private $session;
    protected function __before()
    {
        $this->addSession();
        if ($this->hookNeedMsg) {
            //微信通用授权接口
            $this->wxAuth();
            //登录判断接口
            //$this->checkLogin();
        }
        if ($this->NeedLogin) {
            //判断登录接口
            $this->checkLogin();

        }

    }
    protected function getSession()
    {
        if (null === $this->session) {
            $sess = $this->getLogined();
            if (empty($sess)) {
                $this->session = array();
            } else {
                $this->session = $sess;
            }
        }
        return $this->session;
    }
    /**
     * 接口返回值添加cookie
     *
     */
    protected function addSession()
    {
        $session = $this->getSession();
        return $this->addRet('session', $session);
    }

    /**
     * 微信auth授权
     */
    protected function checkLogin()
    {
        $session = $this->getSession();
        $weixin = new App_Weixin();
        //session空
        $user = new Dao_User();
        $invite = new Dao_Invite();
        $users= $user -> getById($session['id']);
        $invites=$invite -> getById($users['id']);
        $top=$user->getById($invites['rid']);
        
        if($users['reward']<2 && $top['reward']<2)
        {
            throw new Blue_Exception_Redirect('/shop/index/index');
        }
    }
    /**
     * 微信auth授权
     */
    protected function wxAuth()
    {
        $session = $this->getSession();
        //$this->log($session);
        $weixin = new App_Weixin();
        $backUrl =  base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        if (empty($session)) {
			$weixin->getAuth($backUrl);
        }
        else
        {
            
            $user = new Dao_User();
            $users= $user -> getByOpenid($session['openid']);
            if(!$users)
            {
                $weixin->getAuth($backUrl); 
            }
            else
            {
                core_log::debug('---关联---');
                $invite=new Dao_Invite();

                $invites=$invite -> getById($users['id']);
                
                $backUrl=base64_decode($backUrl);
                $re=$this->parse_url_param($backUrl);
                if(!$invites){
                    if(!empty($re['rid']) && $users['id']!=$re['rid'])
                    {
                        core_log::debug('---关联.3---');
                        //关联
                        $lama = new App_Lama();
                        $lama->changeInvite($users['id'],$re['rid']);
                    }
                }
                //获取等级基本信息
                $user = new Dao_Reward();
                $reward= $user -> get($users['reward']);          
                $this->addRet('reward', $reward);
                
                $dUser = new Dao_User();
                $top=$dUser->getById($invites['rid']);
                $this->addRet('focus', $users['focus']);
                $this->addRet('top_reward', $top['reward']);
                
                //SDK
                $sdk= $weixin -> getSDK();          
                $this->addRet('sdk', $sdk);

            }
        } 
    }
    protected function log($data)
    {
        echo "<pre>";
        print_r($data);
        exit();
    }
    
    protected function parse_url_param($str)    
    {  
        $url= parse_url($str);   
        $data = array();      
        $p=array();  
        if(!empty($url['query']))
        {
            $p = explode('&', $url['query']);   
            foreach ($p as $val) {    
                $tmp = explode('=', $val);    
                $data[$tmp[0]] = $tmp[1];    
            }  
        }
  
        return $data;    
    }    
	protected function Warning($message)
    {
        throw new Blue_Exception_Warning($message);
    }
}
