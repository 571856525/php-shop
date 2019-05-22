<?php

/**
 * 菜单入口
 */

class Controller_Menu extends Yaf_Controller_Abstract
{

    public $actions = array(
        'index' => 'actions/menu/Index.php', //微信交互入口
        'auth' => 'actions/menu/Auth.php',//微信授权
        'sdk' => 'actions/menu/Sdk.php',//获取SDK
    );
}
