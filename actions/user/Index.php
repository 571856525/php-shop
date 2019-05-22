<?php

/**
 * 个人中心
 */

class Action_Index extends App_Action
{
    private $sUser;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sUser = new Service_User();
        $this->sDelivery=new Service_Delivery();
        $this->sCoupons=new Service_Coupons();
        $this->setView(Blue_Action::VIEW_JSON);
        $this->setView(Blue_Action::VIEW_SMARTY3);     
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        $user = $this->sUser->getById($sess['id']);
        $weixin = new App_Weixin();
        $sdk = $weixin->getSDK();
        //二维码
        if(empty($user['qrcode']))
        {
            $qrid = Arch_ID::g('qrcode');
            $url = 'http://test.wgxscn.com/shop/index/index?rid='.$sess['id'];//线下
            $qrcode = new App_Qrcode();
            $file = $qrcode->create($url);
            $ins = Arch_Paf::instance('image');
            $data = array(
                'module' => 'cyps',
                'file' => $file
            );
            $r = $ins->call('publish', $data);
            $user['qrcode'] = $r['data']['0'];
                $data=array(
                    'openid' => $sess['openid'],
                    'qrcode' => $user['qrcode']
                );
                Blue_Commit::call('user_Update', $data);
        }

        $data = $this->sDelivery->getList($sess['id'],1,1);
        if($data && $user['reward']>1){
            $status=1;
        }
        
        //是否含有优惠券
        $have=$this->sCoupons->getByPay($sess['id']);
        if(!empty($have)){
            $is_have=1;
        }
        return array('data'=>$user,'sdk'=>$sdk,'delivery'=>$status,'coupons'=>$is_have);
    }
}
