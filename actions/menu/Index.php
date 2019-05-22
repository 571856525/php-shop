<?php

/**
 * 公众号授权入口
 */

class Action_Index extends App_Action
{
	private $weCheat;
	
	public function __prepare()
	{
		$this->weCheat = new App_WeChatCallBack();
		$this->setView(Blue_Action::VIEW_JSON);
	}
	
	public function __execute()
	{    
		core_log::debug('----*************'.$_GET['echostr'].'*****************');
		if(isset($_GET['echostr'])){
			//配置微信服务器
            $this->weCheat->valid($_GET['echostr']);
		}else{
			//微信用户发过来的信息
			$this->weCheat->responseMsg();
		}
	}
}

