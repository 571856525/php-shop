<?php

/**
 * 代理商
 */

class Controller_Agent extends Yaf_Controller_Abstract
{
    public $actions = array(
        'apply' => 'actions/agent/Apply.php',//申请代理商
        'notify' => 'actions/agent/Notify.php',//回调

        'info' => 'actions/agent/Info.php',//
        'list' => 'actions/agent/List.php'//
    );
}
