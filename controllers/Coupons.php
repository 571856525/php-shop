<?php

/**
 * 优惠券接口
 */

class Controller_Coupons extends Yaf_Controller_Abstract
{
    public $actions = array(
        'index' => 'actions/coupons/Index.php',//优惠券列表
        'getlist' => 'actions/coupons/getList.php',//满足的优惠券列表
        'reciving' => 'actions/coupons/Reciving.php',//优惠券领取页
    );
}
