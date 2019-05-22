<?php

/**
 * 收货地址
 * User: Administrator
 * Date: 2016/3/16
 * Time: 18:44
 */

class Controller_Address extends Yaf_Controller_Abstract
{
    public $actions = array(
        'index' => 'actions/address/Index.php', //收货地址列表
        'info' => 'actions/address/Info.php', //收货地址信息
        'create' => 'actions/address/Create.php', //添加收货地址
        'edit' => 'actions/address/Edit.php', //修改收货地址
        'delete' => 'actions/address/Delete.php', //删除收货地址
        'test' => 'actions/address/Test.php', //测试签名
    );
}
