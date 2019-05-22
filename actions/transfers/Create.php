<?php

/**
 * 调拨申请
 */

class Action_Create extends App_Action
{
    private $sTransfers;
    private $sGoods;
    private $sCarts;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->sTransfers = new Service_Transfers();
        $this->sGoods = new Service_Goods();
        $this->sCarts = new Service_Carts();
        $this->setView(Blue_Action::VIEW_JSON);
        
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        if ($this->getRequest()->isGet()) {
            echo "<form class='form-auth-small' method='post' id='loginform'>
                <div class='form-group'>
                  <label for='tel' class='control-label sr-only'>调拨数量</label>
                  <input type='text' class='form-control' id='num' name='num' placeholder='num' maxlength='11'>
                <button type='submit' id='login-btn' class='btn btn-primary btn-lg btn-block'>LOGIN</button> </form> ";
            exit();
        } 
        else
        {

            $carts=$this->sCarts->getById(implode(',',$req['id']));
            foreach($carts as $k=>$v)
            {
                $goods=$this->sGoods->getById($v['goodsid']);
                if($goods)
                {
                    $amount=$amount+$goods['amount']*$req['num'][$k];
                } 
            }


            $ordersn = date('YmdHis') . rand(10000000, 99999999);
            $req=$this->verify();
            $ret = array(
                'id' => $req['id'],
                'ordersn' => $ordersn,
                'userid' => $sess['id'],
                'fromid' => $sess['referees'],
                'addtime'=>time()
            );
            Blue_Commit::call('transfers_Create', $ret);
            
            //获取调拨商品信息
            $ret = array(
                'ordersn' => $ordersn,
                'userid' => $sess['id'],
                'goods' => $req['goods'],
                'addtime'=>time()
            );
            Blue_Commit::call('transfers_Goods', $ret);
            return $ret;
        }

    }
    public function verify()
    {
        $req['goods']=json_encode($_POST);
        if (empty($req['goods'])) {
            throw new Blue_Exception_Warning('调拨信息不能为空');
        }
        return $req;
    }

}
