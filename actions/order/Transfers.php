<?php
/**
 * 调拨来自微信的回调
 * 
 */

class Action_Transfers  extends App_Action
{
	private $sOrder;
	public function __prepare(){
		$this->setView(Blue_Action::VIEW_JSON);
		$this->sTransfers = new Service_Transfers();
	}

	public function __execute(){
		//存储微信的回调
		$notify = new App_Notify();
		$ret = $notify->getXml();
		Core_Log::debug('支付回调成功'.json_encode($ret));
		if($ret){
			//查询订单信息
			$order = $this->sTransfers->getBySn($ret['trade_id']);
			if($trade['status']>=1){
				exit;
			}
			Core_log::Warning('调拨接口回调数据---------->'.json_encode($trade));
			$ret['id'] = $order['id'];
			//修改订单状态和用户等级
			if($trade['activity_key']>0){
				Blue_Commit::call('transfers_Update', $ret);
			}
		}
		exit("<xml>\n<return_code><![CDATA[SUCCESS]]></return_code>\n<return_msg><![CDATA[OK]]></return_msg>\n</xml>");
	}

	public function __complete(){
	}
}
