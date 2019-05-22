<?php

/**
 * 评论接口
 */

class Controller_Comments extends Yaf_Controller_Abstract
{
    public $actions = array(
        'create' => 'actions/comments/Create.php',//生成评论
        'list' => 'actions/comments/List.php',//评论列表
    );
}
