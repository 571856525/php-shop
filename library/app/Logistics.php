<?php
/**
 * 物流相关
 * 
 */
class App_Logistics
{
    private $host;
	private $appCode;
	public function __construct(){
        $this->host = "https://kdwlcxf.market.alicloudapi.com/kdwlcx";
		$this->appCode ='c67f97790043446988f46acbcea70888';
	}

	/**
	 * 获取物流信息
	 */
	public function getShipping($no,$type){
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $this->appCode);
        $querys = "no=".$no."&type=".$type;
        $url = $this->host. "?" . $querys;
		$ret = $this->httpGet($url,$headers);
        $msg = json_decode($ret, true);
        return $msg;
	}

	public function httpGet($url,$headers=null) {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST,  "GET");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
		$res = curl_exec($curl);
		if(empty($res)){
			throw new Blue_Exception_Warning('http get fail', array('url' => $url));
		}
		curl_close($curl);
		return $res;
	}

}
