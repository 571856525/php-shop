<?php

/**
 * 添加/修改收货地址
 */

class Action_Create extends App_Action
{

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sAddress = new Service_Address();
        $this->setView(Blue_Action::VIEW_JSON);
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        if ($this->getRequest()->isGet()) {
            echo "<form class='form-auth-small' method='post' id='loginform'>
                <div class='form-group'>
                  <label for='tel' class='control-label sr-only'>联系人</label>
                  <input type='text' class='form-control' id='contact' name='contact' placeholder='contact' maxlength='11'>
                </div>
                <div class='form-group'>
                  <label for='tel' class='control-label sr-only'>联系人</label>
                  <input type='text' class='form-control' id='mobile' name='mobile' placeholder='mobile' maxlength='11'>
                </div>
                <div class='form-group'>
                  <label for='tel' class='control-label sr-only'>收货地址</label>
                  <input type='text' class='form-control' id='address' name='address' placeholder='address' maxlength='11'>
                </div>
                <input type='hidden' name='id' id='id' value='1'>
                <button type='submit' id='login-btn' class='btn btn-primary btn-lg btn-block'>LOGIN</button> </form> ";
            exit();
        } 
        else
        {
            $req=$this->verify();
            $id = Arch_ID::g('shop_address',false);
            $ret = array(
                'id' => $id,
                'userid' => $sess['id'],
                'contact' => $req['contact'],
                'mobile' => $req['mobile'],
                'address' => $req['address'],
                'detail' => $req['detail']
            );
            Blue_Commit::call('address_Create', $ret);
            return $ret;
        }

    }
    public function verify()
    {
        // $rule = array(
            //'contact' =>array('filterStrlen',array(1,256)),//联系人
            //'mobile' =>array('filterStrlen',array(1,256)),//联系电话
            //'address' =>array('filterStrlen',array(1,256)),//收货地址
            // 'detail' =>array('filterStrlen',array(1,256)),//详细地址
        // );
		// $req = Blue_Filter::filterArray($_POST, $rule);
		$req=$_POST;
        if (empty($req['contact'])) {
            throw new Blue_Exception_Warning('联系人不能为空');
        }
        if (empty($req['mobile'])) {
            throw new Blue_Exception_Warning('联系电话不能为空');
        }
        if (empty($req['address'])) {
            throw new Blue_Exception_Warning('收货地址不能为空');
        }
        if (empty($_POST['detail'])) {
            throw new Blue_Exception_Warning('详细地址不能为空');
		}
        return $req;
    }

}
