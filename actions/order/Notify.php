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
		$this->sOrder = new Service_Order();
	}

	public function __execute(){
		//存储微信的回调
		$notify = new App_Notify();
		$ret = $notify->getXml();
		Core_Log::debug('支付回调成功'.json_encode($ret));
		if($ret){
			//查询订单信息
			$order = $this->sOrder->getByOrder($ret['trade_id']);

			Core_log::Warning('直购支付接口回调数据---------->'.json_encode($trade));
			Core_log::Warning('直购支付接口回调数据---------->'.json_encode($order));
			
			$ret['id'] = $order['id'];
			//修改订单状态和用户等级
			if($order['state']<=0){
				Core_log::Warning('直购支付接口回调数据---------->'.json_encode($ret));
				Blue_Commit::call('order_Update', $ret);
			}
		}
		exit("<xml>\n<return_code><![CDATA[SUCCESS]]></return_code>\n<return_msg><![CDATA[OK]]></return_msg>\n</xml>");
	}

	public function __complete(){
	}
}
