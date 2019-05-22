<?php
/**
 * 短信发送接口
 *
 * @author hufeng<@yunsupport.com>
 * @copyright 2014~ (c) @yunsupport.com
 * @Time: Wed 17 Dec 2014 03:31:33 PM CST
 */

class App_Sms
{
    /**
     * 发送短信
     *
     * @param string $mobile
     * @param string $text 短信正文内容
     *
     * @return boolean
     */
    static public function sendText($mobile, $text){
        $http = new Arch_Http('http://yunpian.com/v1/sms/send.json');

        $data = array(
            'apikey' => '8c7e638848af090bd301f2561f9c31f1',
            'mobile' => $mobile,
            'text' => $text
        );

        $r = $http->post($data);

        $r = json_decode($r, true);

        if(is_array($r) && $r['code'] == 0){
            return 0;
        }

        throw new Blue_Exception_Warning("短信发送失败,请稍后再试", $r);
    }

    /**
     * 发送短信
     *
     * 这里依赖云片网络的模板
     *
     * @param string $mobile
     * @param int $tpl 参考云片网络的模板
     * @param array $value 变量
     *
     * @return boolean 这里返回true，仅表示提交成功了，不一定等于短信发送成功
     */
    static public function send($mobile, $tpl, $value = array()){
        $http = new Arch_Http('http://yunpian.com/v1/sms/tpl_send.json');

        $data = array(
            'apikey' => '8c7e638848af090bd301f2561f9c31f1',
            'mobile' => $mobile,
            'tpl_id' => $tpl,
            'tpl_value' => self::genValue($value),
        );
        $r = $http->post($data);

        $r = json_decode($r, true);

        if(is_array($r) && $r['code'] == 0){
            return 0;
        }

        throw new Blue_Exception_Warning("短信发送失败,请稍后再试");
    }

    static private function genValue($value){
        $ret = array();
        foreach($value as $k => $v){
            $ret[] = sprintf('#%s#=%s', urlencode($k), urlencode($v));
        }

        return implode('&', $ret);
    }
}
