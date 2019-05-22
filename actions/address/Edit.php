<?php

/**
 * 添加/修改收货地址
 */

class Action_Edit extends App_Action
{

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sAddress = new Service_Address();
        $this->setView(Blue_Action::VIEW_SMARTY3);
       
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        if ($this->getRequest()->isGet()) {
            $id=$_GET['id'];
            if(empty($id)){
                $this->Warning('id不能为空');
            }
            $address=$this->sAddress->getById($id);
            return array('address'=>$address);
        } 
        else
        {
            $this->setView(Blue_Action::VIEW_JSON);
            $req=$this->verify();
            $ret = array(
                'id' => $req['id'],
                'contact' => $req['contact'],
                'mobile' => $req['mobile'],
                'address' => $req['address'],
                'detail' => $req['detail']
            );
            Blue_Commit::call('address_Update', $ret);
            return $ret;
        }

    }
    public function verify()
    {
        $rule = array(
            'id' =>array('filterIntBetweenWithEqual', array(0)),//ID
            'contact' =>array('filterStrlen',array(1,256)),//联系人
            'mobile' =>array('filterStrlen',array(1,256)),//联系电话
            'address' =>array('filterStrlen',array(1,256)),//收货地址
            'detail' =>array('filterStrlen',array(1,256)),//收货地址
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        if (empty($req['contact'])) {
            throw new Blue_Exception_Warning('联系人不能为空');
        }
        if (empty($req['mobile'])) {
            throw new Blue_Exception_Warning('联系电话不能为空');
        }
        if (empty($req['detail'])) {
            throw new Blue_Exception_Warning('详细地址不能为空');
        }
        if (empty($req['address'])) {
            throw new Blue_Exception_Warning('收货地址不能为空');
        }
        return $req;
    }

}
