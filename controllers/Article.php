<?php

/**
 * 资讯接口
 */

class Controller_Article extends Yaf_Controller_Abstract
{
    public $actions = array(
        'index' => 'actions/article/Index.php',//新闻列表 
        'info' => 'actions/article/Info.php',//新闻信息
        'show' => 'actions/article/Show.php',//单页申请信息
    );
}
