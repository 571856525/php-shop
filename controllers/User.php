<?php
	
	/**
	 * 用户
	 * User: Administrator
	 * Date: 2016/3/16
	 * Time: 18:44
	 */
	
	class Controller_User extends Yaf_Controller_Abstract
	{
		public $actions = array(
            'index'    => 'actions/user/Index.php',   //首页
			'profile'  => 'actions/user/Profile.php',   //个人资料
			'downlist' => 'actions/user/Downlist.php',   //我的下级
			'send'     => 'actions/user/Send.php',   //发放月度奖励
			'wallet'   => 'actions/user/Wallet.php',   //我的钱包	
			'teaminfo' => 'actions/user/Teaminfo.php',   //团队信息		_
			'apply'    => 'actions/user/Apply.php',   //提现申请
			'cashlist' => 'actions/user/Cashlist.php',   //提现记录表
			'extract' => 'actions/user/Extract.php',   //提成记录表
		);
	}
