<?php

/**
 * 购物车接口
 */

class Controller_Cart extends Yaf_Controller_Abstract
{
    public $actions = array(
        'index' => 'actions/cart/Index.php',//购物车
        'create' => 'actions/cart/Create.php',//更新购物车
        'update' => 'actions/cart/Update.php',//购物车提交购物车
        'delete' => 'actions/cart/Delete.php',//删除购物车
    );
}
