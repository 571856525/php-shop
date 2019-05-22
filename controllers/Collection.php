<?php

/**
 * 收藏接口
 */

class Controller_Collection extends Yaf_Controller_Abstract
{
    public $actions = array(
        'create' => 'actions/collection/Create.php',//收藏
        'index' => 'actions/collection/Index.php',//收藏列表
        'delete' => 'actions/collection/Delete.php',//删除收藏
    );
}
