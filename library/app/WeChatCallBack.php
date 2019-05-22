<?php

/**
 * 微信自定义菜单栏
 */

class App_WeChatCallBack
{
    private $token = 'lamaxiaobao';

    public function valid($data)
    {
        if ($this->checkSignature()) {
            core_log::debug('签名报错'.json_encode($_GET));
            echo $data;
            exit;
        }
    }

	
	 public function __construct()
    {
        $this->dUser = new Dao_User();
        $this->mc = Arch_Memcache::factory('shop');
    }
    /**
     * @return bool
     * 验证微信签名
     */
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 消息回复处理
     */
    public function responseMsg()
    {   
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $time = time();
			
             //写入用户信息
            switch ($postObj->MsgType) {
                case 'event':
                    //判断具体的时间类型（关注、取消、点击）
                    $event = $postObj->Event;
                    if ($event == 'subscribe') { // 关注事件
                        $user = $this->getUser($fromUsername);
                        $this->doSubscribe($postObj);
                    } elseif ($event == 'CLICK') {//菜单点击事件
                        $this->doClick($postObj);
                    } elseif ($event == 'VIEW') {//连接跳转事件
                        $this->doView($postObj);
                    } elseif ($event == 'scan') {//用户已关注时扫描二维码事件
                        $data['ticket'] = $postObj->Ticket;
                    }elseif($event =='unsubscribe'){
						$this->dUser->update(sprintf("openid='%s'", $fromUsername), 'focus=0');
					}
                    break;
                case 'text'://文本消息
                    $this->doText($postObj);
                    break;
            }
        }
    }


    /**
     * 关注后做的事件
     * @param $postObj
     */

    private function doSubscribe($postObj)
    {
        $reply = new Dao_Reply();
        $replyInfo = $reply->getByType(1);//1 是关注事件
        $content = $replyInfo['content'];
        core_log::debug('关注事件');
        $this->msgText($postObj->FromUserName, $postObj->ToUserName, "$content");
    }


	 public function getUser($open)
    {
        $weixin = new App_Weixin();
        //查询用户是否在系统
        $user = $this->dUser->selectOne(sprintf("openid='%s'", $open), 'openid,id,nickname');
        if (empty($user)) {//
            $data = $weixin->getSubscribe($open);
            if (isset($data['errcode'])) {
                throw new Blue_Exception_Warning('拉取关注公众号用户信息失败');
            }
            $user = array(
                'avatar' => $data['headimgurl'],
                'nickname' => $data['nickname'],
                'reward' => 1,
                'openid' => $data['openid'],
                'regtime' => TIMESTAMP,
            );
            $this->dUser->insert($pass, true);
        }
        $this->dUser->update(sprintf("openid='%s'", $user['openid']), 'focus=1');
        return $user;
    }
	
    /**
     * 菜单点击回复
    **/
    public function doClick($postObj)
    {
        switch ($postObj->EventKey) {
            case '6666':
                $contentStr = "课程1";
                $this->msgText($postObj->FromUserName, $postObj->ToUserName, $contentStr);
                break;
            case '7777':
                $contentStr = "课程2";
                $this->msgText($postObj->FromUserName, $postObj->ToUserName, $contentStr);
                break;
            case '8888':
                $contentStr = "课程3";
                $this->msgText($postObj->FromUserName, $postObj->ToUserName, $contentStr);
                break;
            default:
            $contentStr = "未找到相关";
                $this->msgText($postObj->FromUserName, $postObj->ToUserName, $contentStr);
        }
    }

    /**
     * 连接跳转事件
     */

    public function doView($postObj)
    {

    }


    /**
     * 文本消息回复
     */
    public function doText($postObj)
    {
        $keyword = trim($postObj->Content);
        $reply = new Dao_Reply();
        $replyInfo = $reply->getByKey($keyword);
        if(empty($replyInfo)){
            $contentStr = "未找到匹配关键组";
            $this->msgText($postObj->FromUserName, $postObj->ToUserName, $contentStr);
            exit;   
        }else{
            $contentStr = $replyInfo['content'];
            $this->msgText($postObj->FromUserName, $postObj->ToUserName, $contentStr);
            exit;     
        }
    }

    /**
     * 发送文本信息
     * @param  [type] $to      目标用户ID
     * @param  [type] $from    来源用户ID
     * @param  [type] $content 内容
     */

    private function msgText($to, $from, $content)
    {
        $response = sprintf($this->msg_template['text'], $to, $from, time(), $content);
        die($response);
    }

    /**
     * 消息模板
     */

    private $msg_template = array(
        'text' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>',//文本回复XML模板
        'image' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[image]]></MsgType><Image><MediaId><![CDATA[%s]]></MediaId></Image></xml>',//图片回复XML模板
        'music' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[music]]></MsgType><Music><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><MusicUrl><![CDATA[%s]]></MusicUrl><HQMusicUrl><![CDATA[%s]]></HQMusicUrl><ThumbMediaId><![CDATA[%s]]></ThumbMediaId></Music></xml>',//音乐模板
        'news' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>%s</ArticleCount><Articles>%s</Articles></xml>',// 新闻主体
        'news_item' => '<item><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><PicUrl><![CDATA[%s]]></PicUrl><Url><![CDATA[%s]]></Url></item>',//某个新闻模板
    );


    /**
     * @param $url
     * @param null $data
     * @return mixed
     * 模拟表单请求
     */
    public function httpGet($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//把输出转化为字符串，而不是直接输出到屏幕
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);//
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//禁止从服务端进行验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//禁止检查服务器SSL证书
        curl_setopt($curl, CURLOPT_URL, $url);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        $res = curl_exec($curl);
        return $res;
    }


}

