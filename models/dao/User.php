<?php

class Dao_User extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'user');
    }
    //根据ID获取用户信息
    public function getById($id)
    {
        return $this->selectOne(sprintf('id=%d', $id), '*');
    }    
    //根据用户名获取用户信息
    public function getByname($username)
    {
        return $this->selectOne(sprintf("username='%s'", $username), '*');
    }
    //根据openid获取用户信息
    public function getByOpenid($openid)
    {
        return $this->selectOne("openid='" . $openid . "'", '*');
    }
     //根据username和password获取openid
    public function getOpenid($username,$password)
    {
        return $this->selectOne(sprintf('username=%s and password=%s', $username,$password), 'openid');
    }
    //根据ID获取用户等级信息
    public function getByReward($id)
    {
        return $this->selectOne(sprintf('id=%d', $id), 'id,reward');
    }  

    //根据等级获取我的下级列表
    public function getDown($uid)
    {
        return $this->select('status=1 and id in(select uid from invite where end >0 and rid='.$uid.') ', 'id,real_name,nickname,reward,headimgurl,openid', 'order by reward desc ');
    }  

}
