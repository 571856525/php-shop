<?php

/**
 * 调拨信息
 */

class Action_List extends App_Action
{
    private $sTransfers;
    private $sUser;


    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->sTransfers = new Service_Transfers();
        $this->sUser = new Service_User();
        $this->setView(Blue_Action::VIEW_JSON);
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        $rn =!empty($_GET['rn']) ? intval($_GET['rn']) : 10;
        $pn =!empty($_GET['pn']) ? intval($_GET['pn']) : 1;

        $data = $this->sTransfers->getListById($sess['id']);
        foreach ($data as &$datas){
            //获取调拨人信息
            if($datas['fromid']!=$sess['id'])
            {
                    //下级向你调拨
                    $datas['from'] = $this->sUser->getById($datas['fromid']);
                    $datas['type'] = 0;
            }
            else
            {
                //你向上级调拨
                $datas['to'] = $this->sUser->getById($datas['userid']);
                $datas['type'] = 1;
            }
            $datas['addtime'] = date("Y-m-d H:i:s",$datas['addtime']);
            

        }
        $weixin = new App_Weixin();
        $sdk = $weixin->getSDK();
        return array('data' => $data,'sdk'=>$sdk);
        //$count= $this->sTransfers->getCount($classid);
        //return array('data' => $data, 'page' => Blue_Page::pageInfo($count, $pn, $rn));
    }

}
