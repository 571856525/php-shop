<?php
class JSSDK {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
	Core_Log::debug('=================init with weixin jssdk', array('appid' => $appId, 'appsecret' => $this->appSecret));
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();
    // 注意 URL 一定要动态获取，不能 hardcode.
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//	$url = 'http://test.guirenli.cn'.$_SERVER['REQUEST_URI'];
	$timestamp = time();
	$nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
	  "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
	);
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

	private function getJsApiTicket() {
		$mc = Arch_Memcache::factory('shop');
		$data = $mc->get('shop_weixin_apiticket');
		if($data){
			Core_Log::debug('=============  apiticket is exist' . json_encode($data));
			return $data;
		}
		$accessToken = $this->getAccessToken();
		// 如果是企业号用以下 URL 获取 ticket
	//  $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
		$url = sprintf("https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=%s", urlencode($accessToken));
		$res = json_decode($this->httpGet($url));
		if(empty($res) || $res->errcode > 0){
			throw new Blue_Exception_Fatal('weixin get ticket fail', $res);
		}
		Core_Log::debug('============== ticket res ' . json_encode($res));
		$data = $res->ticket;
		$mc->set('shop_weixin_apiticket', $data, 7199);
		return $data;
	} 

  private function getAccessToken() {
	  $mc = Arch_Memcache::factory('shop');
	  $data = $mc->get('shop_weixin_accesstoken');
	  $data = false;
	  if($data){
		  return $data;
	  }else{
		  $url = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', $this->appId, $this->appSecret);
		  $res = json_decode($this->httpGet($url), true);
		  if(empty($res) || $res['errcode'] > 0){
			  throw new Blue_Exception_Fatal('weixin get accesstoken fail', $res);
		  }
		  $data = $res['access_token'];
		  $mc->set('shop_weixin_accesstoken', $data, 3600);	//默认缓存1个小时
		  Core_Log::warning("fresh mc");
		  return $data;
	  }
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    if(empty($res)){
    	throw new Blue_Exception_Fatal('http get fail', array('url' => $url));
    }
    curl_close($curl);
    return $res;
  }
  
  	/**
  	 * 微信公众平台上传音频
	 *
  	 * */
	public function uploadAudio($url){
		$access_token = $this->getAccessToken();
		$type = "voice";
		$filedata = array("media"  => "@".$url);
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=$type";
		$result = https_request($url, $filedata);
		return $result;
	}
	/**
  	 * 微信公众平台上传图片
	 *
  	 * */
	public function uploadImage($url){
		$access_token = $this->getAccessToken();
		$type = "image";
		$filedata = array("media"  => "@".$url);
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=$type";
		$result = https_request($url, $filedata);
		return $result;
	}
	/**
	 * 微信公众平台下载音频
	 *
	 * */
	public function DownAudio($mediaid){
		$access_token = $this->getAccessToken();
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$mediaid";
		$fileInfo = $this->downloadWeixinFile($url);

		if(empty($fileInfo['body']) || '{' == $fileInfo['body'][0]){
			throw new Blue_Exception_Warning('下载语音失败', array('msg' => $fileInfo));
		}

		$file = sprintf('/tmp/cypsaudio/%s.amr', md5($fileInfo['body']));
		$dir = dirname($file);
		if(is_dir($dir) == false){
			  if(mkdir($dir, 0755, true) == false){
		              throw new Blue_Exception_Warning('创建目录失败', array('dir' => $dir));
				}
		}
		if(file_put_contents($file, $fileInfo['body']) == false){
			throw new Blue_Exception_Warning('数据写入到文件失败', array('file' => $file));
		}
		return $file;
	}

	/**
	 * 微信公众平台下载图片
	 *
	 * */
	public function DownPhoto($mediaid){
		$access_token = $this->getAccessToken();
		$url = sprintf('http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=%s&media_id=%s', urlencode($access_token), urlencode($mediaid));;
		$fileInfo = $this->downloadWeixinFile($url);
		$head = $fileInfo['header'];

		if($head['http_code'] != 200 || $head['content_type'] == 'text/plain'){
			$body = json_decode($fileInfo['body'], true);
			throw new Blue_Exception_Warning('图片获取失败', $body);
		}
		return $fileInfo['body'];
	}

	/**
	 * 上传音频
	 * 
	 * 
	 */
	public function UpAudio($url){
		//上传语音
		$type = "voice";
		$access_token = $this->getAccessToken();
		$filedata = array("file1"  => "@".$url);
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=$type";
		$result = $this->https_request($url, $filedata);
		var_dump($result);
	}
	
	
	private function downloadWeixinFile($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$package = curl_exec($ch);
		$httpinfo = curl_getinfo($ch);
		curl_close($ch);
		$imageAll = array_merge(array('header' => $httpinfo), array('body' => $package));
		return $imageAll;
	}
	
	private function saveWeixinFile($filename, $filecontent)
	{
		$local_file = fopen($filename, 'w');
		if (false !== $local_file){
			if (false !== fwrite($local_file, $filecontent)) {
				fclose($local_file);
			}
		}
	}
  
	public function https_request($url, $data = null)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
}

