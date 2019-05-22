<?php

/**
 * 调拨申请
 * User: Administrator
 * Date: 2016/3/16
 * Time: 18:44
 */

class Controller_Transfers extends Yaf_Controller_Abstract
{
    public $actions = array(
        'index' => 'actions/transfers/Index.php', //系统云仓信息
        'list' => 'actions/transfers/List.php', //调拨信息
        'create' => 'actions/transfers/Create.php', //调拨申请
        'callback' => 'actions/transfers/Callback.php',//支付结果
        'audit' => 'actions/transfers/Audit.php',//调拨审批
        'notify' => 'actions/transfers/Notify.php',//回调接口
        'delivery' => 'actions/transfers/Delivery.php',//发货接口
        'pay' => 'actions/transfers/Pay.php',//未付款产生的订单在支付接口
        'purchase' => 'actions/transfers/Purchase.php',//进货列表
        'detaile' => 'actions/transfers/Detaile.php',//进出货
        'cancel' => 'actions/transfers/Cancel.php',//取消订单
        'info' => 'actions/transfers/Info.php',//取消订单
        'auperior' => 'actions/transfers/Auperior.php'//普通用户的上级
    );
}
