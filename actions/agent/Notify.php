<?php
/**
 * 直购来自微信的回调
 * 
 */

class Action_Notify extends App_Action
{
	private $sOrder;
	public function __prepare(){
		$this->setView(Blue_Action::VIEW_JSON);
		$this->sTrade = new Service_Trade ();
	}

	public function __execute(){
		//存储微信的回调
		$notify = new App_Notify();
		$ret = $notify->getXml();
		Core_Log::debug('支付回调成功151515'.json_encode($ret['trade_id']));
		if($ret){
			//查询订单信息
			Core_log::debug('直购支付接口回调数据---------->');
			$trade = $this->sTrade->getByTrade($ret['trade_id']);

			Core_log::debug('直购支付接口回调数据---------->'.json_encode($trade));
			
			$ret['id'] = $trade['id'];
			$ret['tradesn']=$ret['trade_id'];
			//修改订单状态和用户等级
			if($trade['status']<=0){
				Core_log::debug('直购支付接口回调数据---------->'.json_encode($ret));
				Blue_Commit::call('trade_Update', $ret);
			}
		}
		exit("<xml>\n<return_code><![CDATA[SUCCESS]]></return_code>\n<return_msg><![CDATA[OK]]></return_msg>\n</xml>");
	}

	public function __complete(){
	}
}
