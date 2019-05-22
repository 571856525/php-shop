<?php

class Service_User
{
    private $dUser;

    public function __construct()
    {
        $this->dUser = new Dao_User();
    }
    //根据ID获取用户信息
    public function getById($id)
    {
        return $this->dUser->getById($id);
    }    
    //根据用户名获取用户信息
    public function getByname($username)
    {
        return $this->dUser->getByname($username);
    }
    //根据openid获取用户信息
    public function getByOpenid($openid)
    {
        return $this->dUser->getByOpenid($openid);
    }
     //加密
    public function password($pass)
    {
        return md5(md5($pass));
    }

    //根据等级获取我的下级列表
    public function getDown($uid){
        return $this->dUser->getDown($uid);
    }
}
