<?php

/**
 * 余额提现
 */

class Controller_Apply extends Yaf_Controller_Abstract
{
    public $actions = array(
        'index' => 'actions/apply/Index.php',//提现记录 
        'create' => 'actions/apply/Create.php',//提现
    );
}
