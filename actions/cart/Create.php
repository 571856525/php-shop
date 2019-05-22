<?php

/**
 * 加入/修改购物车
 */

class Action_Create extends App_Action
{
    private $sCart;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sCart = new Service_Cart();
    }

    public function __execute()
    {
        
        $session = $this->getSession();
        if ($this->getRequest()->isGet()) {
            echo "<form class='form-auth-small' method='post' id='loginform'>
                <div class='form-group'>
                  <label for='tel' class='control-label sr-only'>商品id</label>
                  <input type='text' class='form-control' id='goodsid' name='goodsid' placeholder='goodsid' maxlength='11'>
                </div>
                <div class='form-group'>
                  <label for='tel' class='control-label sr-only'>数量</label>
                  <input type='text' class='form-control' id='num' name='num' placeholder='num' maxlength='11'>
                </div>
                <button type='submit' id='login-btn' class='btn btn-primary btn-lg btn-block'>LOGIN</button> </form> ";
            exit();
        } 
        else
        {
            $req=$this->verify(); 
            $req['userid']=$session['id'];
            $req['addtime']=time();
            Blue_Commit::call('cart_Create', $req);
            return array('data' => $req);
        }
    }
    public function verify()
    {
        $rule = array(
            'goodsid' => array('filterIntBetweenWithEqual', array(0)),
            'num' => array('filterIntBetweenWithEqual', array(0))
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }

}
