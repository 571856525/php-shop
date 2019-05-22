<?php

/**
 * 我的收货地址
 */

class Action_Test extends App_Action
{
    private $sUser;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sAddress = new Service_Address();
        $this->setView(Blue_Action::VIEW_SMARTY3);
        
    }

    public function __execute()
    {
      
        $signature = '779ab389d80e1bd9c9f9f13115e42573c889048e';
        $timestamp = '1540281979';
        $nonce = '582982383';
        $token = 'lamaxiaobao';
        $tmpArr=array($token,$timestamp,$nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr==$signature){
             echo 'true';die;
        }else{
            echo 'false';die;
        }
    }

}
