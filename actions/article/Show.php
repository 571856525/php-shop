<?php

/**
 * 单页申请
 */

class Action_Show extends App_Action
{
    private $sArticle;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sArticle = new Service_Article();
        $this->sUser = new Service_User();
        $this->setView(Blue_Action::VIEW_JSON);
    }
    public function __execute()
    {
        //id 34为申请合伙人   35为申请分公司
        $type =array('34'=>'3','35'=>'4');
        $session = $this->getSession();
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY2);
            $user = $this->sUser->getById($session['id']);
            $id =!empty($_GET['id']) ? intval($_GET['id']) : 0;
            if(empty($id))
            {
                $this->Warning('ID不能为空');
            }
            $data = $this->sArticle->getById($id);
            $weixin = new App_Weixin();
            $sdk = $weixin->getSDK();
            return array('data' => $data,'user' => $user,'type' => $type[$id],'sdk'=>$sdk);
        }
        else
        {
            $req = $this->verify();
            $ret = array(
                'id' => $session['id'],
                'real_name' => $req['real_name'],
                'mobile' => $req['mobile'],
                'approval' => $req['type']
            );
            Blue_Commit::call('user_Approval', $ret);
            return $ret;
        }        
    }
    public function verify()
    {
        $rule = array(
            'type' => array('filterIntBetweenWithEqual', array(0)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        $req['real_name']=$_POST['real_name'];
        $req['mobile']=$_POST['mobile'];
        return $req;
    }

}
