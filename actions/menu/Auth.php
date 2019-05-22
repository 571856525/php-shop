<?php

/**
 * 公众号授权入口
 */

class Action_Auth extends App_Action
{
    private $weCheat;
    private $sUser;
    private $sInvite;

    public function __prepare()
    {
        $this->setView(Blue_Action::VIEW_JSON);
        $this->weCheat = new App_WeChatCallBack();
        $this->sUser = new Service_User();
        $this->sInvite = new Service_Invite();
    }

    public function __execute()
    {
        $code = $_GET['code'];
        $backUrl = base64_decode($_GET['backUrl']);
        $weixin = new App_Weixin();
        if (empty($code)) {
            $this->Warning('用户未授权', array('code' => $code, 'state' => $state));
        }
        $data = $weixin->getToken($code);
        if (empty($data)) {
            $this->Warning('微信数据返回错误', array('data' => $data));
        }
        $data = json_decode($data, true);

        //获取用户高级接口
        //$user = $weixin->getSubscribe($data['openid']); //获取用户信息
        //$this->log($user);
        
        $user = $weixin->getUser($data); //获取用户信息
        $user = json_decode($user, true);
        if (empty($user['openid'])) {
            $this->Warning('微信数据返回错误', array('user' => $user));
        }
        $openid =!empty($_GET['openid']) ? trim($_GET['openid'])  : '';
        $referees=0;

        
        //判断此用户是否在数据库中存在
        $userinfo = $this->sUser->getByOpenid($user['openid']);
        //用户不存在写入数据库
        if (empty($userinfo)) {
            $pass = array(
                'openid'     => $user['openid'],
                'nickname'   => $user['nickname'],
                'headimgurl' => $user['headimgurl'],
                'regtime'    => time(),
            );
            Blue_Commit::call('user_Create', $pass);
            $userinfo = $this->sUser->getByOpenid($user['openid']);
        } else {
            
            $pass = array(
                'openid' => $user['openid'],
                'nickname' => $user['nickname'],
                'headimgurl' => $user['headimgurl'],
            );
            Blue_Commit::call('user_Update', $pass);
        }
        //关联用户
        $re=$this->parse_url_param($backUrl);
        $rid = !empty($re['rid']) ? $re['rid'] : 0 ;

        $lama = new App_Lama();
        $lama->changeInvite($userinfo['id'],$rid);

        $this->setLogin($userinfo, 86400 * 30); //写入session
        header('location:' . $backUrl);
    }

}
