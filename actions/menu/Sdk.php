<?php

/**
 * 获取SDK
 */

class Action_Sdk extends App_Action
{
    public function __prepare()
    {
        $this->setView(Blue_Action::VIEW_JSON);
        $this->weixin = new App_Weixin();
    }

    public function __execute()
    {
        $sdk = $this->weixin->getSDK();
        return array('sdk' => $sdk);
    }

}
