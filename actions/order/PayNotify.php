<?php
/**
 * 充值支付成功的回调
 * 
 */

class Action_PayNotify extends App_Action
{
	private $dTransfers;
	public function __prepare(){
		$this->setView(Blue_Action::VIEW_JSON);
		$this->sTrade = new Service_Trade();
	}

	public function __execute(){
		core_log::debug('---------');
		//存储微信的回调
		$notify = new App_Notify();
		$ret = $notify->getXml();
		Core_Log::debug('充值支付回调成功'.json_encode($ret));
		if(!empty($ret)){
			//查询订单信息
			
			$trade = $this->sTrade->getByTrade($ret['trade_id']);
			Core_Log::debug('充值支付回调成功'.json_encode($trade));
			$res['id'] = $trade['id'];
			// //修改订单状态和用户等级
			if($trade['state']<=0){
				Core_log::Warning('充值支付接口回调数据222---------->'.json_encode($ret));
				//更新订单状态
				Blue_Commit::call('trade_Update', $res);
			}
		}
		exit("<xml>\n<return_code><![CDATA[SUCCESS]]></return_code>\n<return_msg><![CDATA[OK]]></return_msg>\n</xml>");
	}

	public function __complete(){
	}
}
