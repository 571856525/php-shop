<?php

/**
 * 功能简介
 */

class Controller_Index extends Yaf_Controller_Abstract
{
    public $actions = array(
        'index' => 'actions/index/Index.php', //首页
        'login' => 'actions/index/Login.php', //登录
        'reg' => 'actions/index/Reg.php', //注册
    );
}
