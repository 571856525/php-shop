<?php
/**
 * 调拨来自微信的回调
 * 
 */

class Action_Notify extends App_Action
{
	private $dTransfers;
	public function __prepare(){
		$this->setView(Blue_Action::VIEW_JSON);
		$this->dTransfers = new Service_Transfers();
	}

	public function __execute(){
		//存储微信的回调
		$notify = new App_Notify();
		$ret = $notify->getXml();
		//Core_Log::debug('调拨支付回调成功'.json_encode($ret));
		if(!empty($ret)){
			//查询订单信息
			$transfers = $this->dTransfers->getBySn($ret['trade_id']);
			$res['id'] = $transfers['id'];
			// //修改订单状态和用户等级
			if($transfers['state']<=0){
				Core_log::Warning('调拨支付接口回调数据222---------->'.json_encode($ret));
				//更新订单状态
				Blue_Commit::call('transfers_Update', $res);
			}
		}
		exit("<xml>\n<return_code><![CDATA[SUCCESS]]></return_code>\n<return_msg><![CDATA[OK]]></return_msg>\n</xml>");
	}

	public function __complete(){
	}
}
