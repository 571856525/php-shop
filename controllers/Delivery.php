<?php

/**
 * 发货接口
 */

class Controller_Delivery extends Yaf_Controller_Abstract
{
    public $actions = array(
        'list' => 'actions/delivery/List.php',//我的发货列表
        'info' => 'actions/delivery/Info.php',//我的发货详情
    );
}
