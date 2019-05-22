<?php
/**
 * 微信支付
 * @author xuefei<@yunbix.com>
 * @copyright 2016~ (c) @yunbix.com
 * @Time: 2016/3/19 PM 12:32
 */
include_once("pay/WxPayPubHelper.php");
class App_Pay
{
	public function getPrepayId($openid, $trade_id, $money, $t){
		//读取配置文件
		$weixin = Arch_Yaml::get('weixin');
		core_log::debug('dingdan'.json_encode($weixin));
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		//设置统一支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写

		$unifiedOrder->setParameter("openid","$openid");//商品描述
		//自定义订单号，此处仅作举例
		$timeStamp = time();
		$out_trade_no = $weixin['pay']['appId']."$timeStamp";//公众号的appid
		$unifiedOrder->setParameter("out_trade_no","$trade_id");//商户订单号
		$unifiedOrder->setParameter("total_fee","$money");//总金额
		if($t == 1){
			$unifiedOrder->setParameter("body","商城购买");//商品描述
			$unifiedOrder->setParameter("notify_url",$weixin['pay']['url']);//通知回调地址
		}else if($t == 2){
			$unifiedOrder->setParameter("body","云仓调拨");//商品描述
			$unifiedOrder->setParameter("notify_url",$weixin['pay']['url_b']);//通知回调地址
		}else if($t == 3){
			$unifiedOrder->setParameter("body","会员充值");//商品描述
			$unifiedOrder->setParameter("notify_url",$weixin['pay']['url_r']);//通知回调地址
		}else if($t == 4){
            $unifiedOrder->setParameter("body","普通用户购买上级商品");//商品描述
			$unifiedOrder->setParameter("notify_url",$weixin['pay']['url_rid']);//通知回调地址 
		}
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
		//$unifiedOrder->setParameter("device_info","XXXX");//设备号
		//$unifiedOrder->setParameter("attach","XXXX");//附加数据
		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID
		$prepay = $unifiedOrder->getPrepayId();

		core_log::debug('********************dingdan'.json_encode($prepay));
		core_log::debug('********************dingdan'.$prepay['err_code_des']);
		core_log::debug($prepay['appid']);
		//生成签名
		$nonceStr = $unifiedOrder->createNoncestr();     //随机字符串
		$arr = array(
			'appId' => $prepay['appid'],
			'timeStamp' => (string)time(),
			'nonceStr' => $nonceStr,
			'package' => 'prepay_id='.$prepay['prepay_id'],
			'signType' => 'MD5'
		);
		$sign = $unifiedOrder->getSign($arr);
		core_log::debug($prepay['prepay_id']);
		$arr = array('prepay_id' => $prepay['prepay_id'], 'appId' => $arr['appId'], 'timeStamp' => $arr['timeStamp'], 'nonceStr' => $arr['nonceStr'], 'paySign' => $sign);
		Core_Log::debug('return:'.json_encode($arr));
		return $arr;
	}
    public function getPrepayId1($openid, $trade_id, $money)
	{
        //读取配置文件
        $weixin = Arch_Yaml::get('weixin');
        //使用统一支付接口
        $unifiedOrder = new UnifiedOrder_pub();
        $unifiedOrder->setParameter("openid","$openid");//商品描述
        $unifiedOrder->setParameter("body","分社购买");//商品描述
        $timeStamp = time();
        $out_trade_no = $weixin['pay']['appId']."$timeStamp";
        $unifiedOrder->setParameter("out_trade_no","$trade_id");//商户订单号
        $unifiedOrder->setParameter("total_fee","$money");//总金额
        $unifiedOrder->setParameter("notify_url",$weixin['pay']['url_b']);//通知地址
        $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
        $prepay = $unifiedOrder->getPrepayId();
        core_log::debug($prepay['appid']);
        //生成签名
        $nonceStr = $unifiedOrder->createNoncestr();     //随机字符串
        $arr = array(
            'appId' => $prepay['appid'],
            'timeStamp' => (string)time(),
            'nonceStr' => $nonceStr,
            'package' => 'prepay_id='.$prepay['prepay_id'],
            'signType' => 'MD5'
        );
        $sign = $unifiedOrder->getSign($arr);
        core_log::debug($prepay['prepay_id']);
        $arr = array('prepay_id' => $prepay['prepay_id'], 'appId' => $arr['appId'], 'timeStamp' => $arr['timeStamp'], 'nonceStr' => $arr['nonceStr'], 'paySign' => $sign);
        Core_Log::debug('return:'.json_encode($arr));
        return $arr;
    }
}
?>
