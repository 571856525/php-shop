<?php
	
	/**
	 * 微信支付回调
	 * @author xuefei<@yunbix.com>
	 * @copyright 2016~ (c) @yunbix.com
	 * @Time: 2016/3/19 PM 16:32
	 */
	
	include_once("pay/WxPayPubHelper.php");
	
	class App_Notify
	{
		public function getXml ()
		{
			//使用通用通知接口
			$notify = new Notify_pub();
			$xml = file_get_contents("php://input");//获取微信返回信息
			//验证微信返回信息
			if(empty($xml)) {
				throw new Blue_Exception_Fatal('weixin get data fail', $xml);
			}
			$postObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);//把xml转换为对象
			$status = $postObj->return_code;
			$openid = $postObj->openid;
			$trade_id = $postObj->out_trade_no;
			//验证签名，并回应微信。
			//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
			//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
			//尽可能提高通知的成功率，但微信不保证通知最终能成功。
			if($postObj->result_code == 'SUCCESS') {
				$notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
			} else {
				$notify->setReturnParameter("return_code", "FAIL");//返回状态码
				$notify->setReturnParameter("return_msg", "签名失败");//返回信息
			}
			$returnXml = $notify->returnXml();
			return array('openid' => $openid, 'trade_id' => $trade_id);
		}
	}
