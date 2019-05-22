<?php
/**
 * 微信相关
 * 
 */
require_once(dirname(__FILE__) . '/weixin/' . 'jssdk.php');
class App_Weixin
{
	private $appId;
	private $appSecret;
	private $url;
	private $qr_url;
	public function __construct(){
		//读取配置文件
		$weixin = Arch_Yaml::get('weixin');
		$this->appId = $weixin['empower']['appId'];
		$this->appSecret = $weixin['empower']['appSecret'];
		$this->redirect_uri = $weixin['empower']['redirect_uri'];
		$this->url = $weixin['empower']['url'];//用户授权
	}

	/**
	 * 获取微信JS-SDK
	 *
	 */
	public function getSDK(){
		Core_Log::debug('=================' . $this->appSecret);
		$jssdk = new JSSDK($this->appId, $this->appSecret);
		$signPackage = $jssdk->GetSignPackage();
		return $signPackage;
	}
	/*
	 *  登录回调
	 */
	public function getBack(){
		$backUrl =  base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$url = "/shop/index/login?backUrl=".$backUrl;
		header("Location:".$url);
		exit;
	}

	/*
	 *  通用的授权接口
	 */
	public function getAuth($backUrl){
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appId."&redirect_uri=".urlencode($this->redirect_uri).'?backUrl='.urlencode($backUrl)."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
		header("Location:".$url);
		exit;
	}
	/*
	 * 通过code获取openid和token
	 */
	public function getToken($code){
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appId.'&secret='.$this->appSecret.'&code='.$code.'&grant_type=authorization_code';
		$data = $this->httpGet($url);
		return $data;
	}
	/*
	 *根据token和openid获取用户信息
	 */

	public function getUser($data){
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$data['access_token'].'&openid='.$data['openid'].'&lang=zh_CN';
		$data = $this ->httpGet($url);
		return $data;
	}
	/*
	 * 获取token
	 */
	public function Token($err=null){
		$mc = Arch_Memcache::factory('shop');
		$token = $mc->get('shop_weixin_accesstoken');
		Core_Log::debug('111token:'.$token);
		if(empty($token) || !empty($err)){
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res = json_decode($this->httpGet($url));
			if(empty($res) || $res->errcode > 0){
				throw new Blue_Exception_Fatal('weixin get accesstoken fail', $res);
			}
			$token = $res->access_token;
			$mc->set('shop_weixin_accesstoken', $token,7200);
		}
		return $token;
	}
	/* 
	 * 客服发送消息
	 */
	public function pushMsg($open,$type,$data){
		$token = $this->Token();		
		$msg = array('touser' =>(string)$open);
		switch($type){
		case 'text':
			$msg['msgtype'] = 'text';
			$msg['text'] = array('content'=> $data);
			break;
		case 'image':
			$msg['msgtype'] = 'image';
			$msg['image'] = array('media_id'=>$data);
			break;
		case 'news':
			$msg['msgtype'] = 'mpnews';
			$msg['mpnews'] = array('media_id'=>$data);
			break;
		case 'voice':
			$msg['msgtype'] = 'voice';
			$msg['voice'] = array('media_id'=>$data);
			break;
		case 'video':
			$msg['msgtype'] = 'video';
			$msg['video'] = array('media_id'=>$data);	
			break;
		}
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$token";
		$data = (array)json_decode($this ->httpGet($url,json_encode($msg,JSON_UNESCAPED_UNICODE)));
		if($data['errcode'] ==40001){
			$token = $this->Token($data['errcode']);
			$data = (array)json_decode($this ->httpGet($url,json_encode($msg,JSON_UNESCAPED_UNICODE)));
		}
		core_log::debug('tupian8888888888888---------------'.json_encode($data));
		return $data;
	}
	/*
	 * 通过media_id获取素材
	 */
	public function getMaterVideo($media){
		$token = $this->Token();
		$url = "https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=$token";
		$data = (array)json_decode($this ->httpGet($url,json_encode($media)));
		return $data;
	}
	/*
	 * 生成永久二维码
	 * 识别id
	 */
	public function setQrcode($scene_type,$id){
		$token = $this->Token();
		switch($scene_type){
		case 'QR_LIMIT_SCENE': //永久
			$data = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
			break;
		case 'QR_SCENE': 
			$data = '{"expire_seconds": 2592000, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
			break;
		}
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
		unset($id);
		$data =(array)json_decode($this ->httpGet($url,$data));
		if($data['errcode'] == 40001){
			$token = $this->Token($data['errcode']);
			$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
			$data =(array)json_decode($this ->httpGet($url,$data));
		}
		if(empty($data['url'])){
			throw new Blue_Exception_Warning('生成永久二维码失败');
		}
		return $data;
	}
	/*
	 * 获取titcket二维码图片
	 */
	public function QrcodeTi($ti){
		 $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".UrlEncode($ti);
		 $data =$this ->httpGet($url);
		 return $data;
	}
	/**
	 * 微信公众平台上传图片
	 *
	 * */
	public function uploadImage($file,$id){
		$token = $this->Token();
		//创建图片的实例
		$ur = '/home/work/pdp/data/app/cyps/image/'.$id.'.jpg';
		unset($id);
		$put = file_put_contents($ur,$file);
		$filedata = array("media"  => "@".$ur);
		$url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=$token&type=image";
		$result = (array)json_decode($this ->httpGet($url,$filedata));
	    core_log::debug('----------------$$$$$$$$$'.json_encode($result));
		return $result;
	}
	public function get_between($input, $start, $end) {
		$substr = substr($input, strlen($start)+strpos($input, $start),
			(strlen($input) - strpos($input, $end))*(-1));
		return $substr;
	}
	/*
	 * 获取微信素材库文件
	 */
	public function getMediaFile($media){
		$token = $this->Token();
		$url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=$token&media_id=$media";
		$result = (array)json_decode($this ->httpGet($url));
		return !isset($result['errcode']) ? 0 :1;
	}
	/*
	 * 微信素材库下载图片
	 */
	public function DownPhoto($mediaid){
		$jssdk = new JSSDK($this->appId, $this->appSecret);
		$url = $jssdk->DownAudio($mediaid);
		return $url;
	}	
	/*
	 *根据mediaid获取下载录音
	 */
	public function DownAudio($mediaid){
		$jssdk = new JSSDK($this->appId, $this->appSecret);
		$url = $jssdk->DownAudio($mediaid);
		return $url;
	}
	/**
	 * 获取用户是否关注
	 */
	public function getSubscribe($openid){
		$token = $this->Token();
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid&lang=zh_CN";
		$ret = $this ->httpGet($url);
		return (array)json_decode($ret);
	}

	/**
	 * 发送通知
	 */
	public function setNotice($data){
		$mc = Arch_Memcache::factory('shop');
		$token = $mc->get('shop_weixin_accesstoken');
		if(empty($token)){
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res = json_decode($this->httpGet($url));
			if(empty($res) || $res->errcode > 0){
				throw new Blue_Exception_Fatal('weixin get accesstoken fail', $res);
			}
			$token = $res->access_token;
			$mc->set('shop_weixin_accesstoken', $token, 600);
		}
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$token;
		$ret = $this ->httpGet($url, $data);
		$msg = json_decode($ret, true);
		if($msg['errcode'] != 0){
			Core_Log::warning('通知发送失败'.$ret);
		}
	}

	public function httpGet($url, $data=null) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//把输出转化为字符串，而不是直接输出到屏幕
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);//
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//禁止从服务端进行验证
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//禁止检查服务器SSL证书
		curl_setopt($curl, CURLOPT_URL, $url);
		if(!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		$res = curl_exec($curl);
		if(empty($res)){
			throw new Blue_Exception_Warning('http get fail', array('url' => $url));
		}
		curl_close($curl);
		return $res;
	}

}
