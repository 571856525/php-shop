<?php

/**
 * 日常消息
 */

class Controller_Returns extends Yaf_Controller_Abstract
{
    public $actions = array(
        'apply' => 'actions/returns/Apply.php',
        'list' => 'actions/returns/List.php',
        'info' => 'actions/returns/Info.php',
    );
}
