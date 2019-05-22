<?php

/**
 * 订单接口
 */

class Controller_Order extends Yaf_Controller_Abstract
{
    public $actions = array(
        'index' => 'actions/order/Index.php',//列表
        'create' => 'actions/order/Create.php',//购物车购买
        'buy' => 'actions/order/Buy.php',//直接购买
        'info' => 'actions/order/Info.php',//订单详情
        'callback' => 'actions/order/Callback.php',//支付结果
        'notify' => 'actions/order/Notify.php',//回调接口
        'test' => 'actions/order/Test.php',//测试
        'lists' => 'actions/order/Lists.php',//某一个用户的订单详情
        'show' => 'actions/order/Show.php',//某一个用户的订单详情

        'make' => 'actions/order/Make.php',//套餐购买
        'recharge' => 'actions/order/Recharge.php',   //充值表
        'paynotify' => 'actions/order/PayNotify.php',   //充值成功回调
    );
}
