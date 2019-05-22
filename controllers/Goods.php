<?php

/**
 * 商品接口
 */

class Controller_Goods extends Yaf_Controller_Abstract
{
    public $actions = array(
        'index' => 'actions/goods/Index.php',//商品列表 
        'info' => 'actions/goods/Info.php',//商品信息
        'stock' => 'actions/goods/Stock.php',//库存返回

        'comments' => 'actions/goods/Comments.php',//商品评论
    );
}
