<?php

/**
 * 调拨审批
 */

class Action_Audit extends App_Action
{
    private $sTransfers;
    private $sCarts;
    private $sWarehouse;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->sTransfers = new Service_Transfers();
        $this->sCarts = new Service_Carts();
        $this->sGoods = new Service_Goods();
        $this->sWarehouse = new Service_Warehouse();
        $this->setView(Blue_Action::VIEW_JSON);     
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        if ($this->getRequest()->isGet()) {
            echo "<form class='form-auth-small' method='post' id='loginform'>
                <div class='form-group'>
                <label for='tel' class='control-label sr-only'>调拨单号</label>
                <input type='text' class='form-control' id='id' name='id' placeholder='id' maxlength='11'>
                <label for='tel' class='control-label sr-only'>审批状态</label>
                <input type='text' class='form-control' id='audit' name='audit' placeholder='audit' maxlength='11'>
                <button type='submit' id='login-btn' class='btn btn-primary btn-lg btn-block'>LOGIN</button> </form> ";
            exit();
        } 
        else
        {
            $req=$this->verify();
            //调拨信息
            $transfers=$this->sTransfers->getById($req['id']);
            if($transfers['audit']>=1)
            {
                $this->Warning('已审批');
            }

            //判断库存
            $carts =$this->sCarts->getByOrderId($transfers['id']);
            //判断
            if(!empty($carts))
            {
                foreach($carts as $v)
                {
                    $warehouse= $this->sWarehouse ->getBygoodsId($transfers['fromid'],$v['goodsid']);
                    $goods= $this->sGoods->getInfo($v['goodsid']);
                    if($warehouse['on_inventory']<$v['num'])
                    {
                        $this->Warning('你的“'.$goods['goodsname'].'”库存不足');
                    }
                }
                //获取上级库存 
            }  

            $res=array(
                'id' => $req['id'],
                'audit' => $req['audit']
            );
            Blue_Commit::call('transfers_Audit', $res);
            return $ret;
        }
    }
    public function verify()
    {
        $rule = array(
            'id' =>array('filterIntBetweenWithEqual',array(0)),//调拨单号
            'audit' =>array('filterIntBetweenWithEqual',array(0))//审批/未审批
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        if (empty($req['id'])) {
            $this->Warning('参数不能为空');
        }
        return $req;
    }

}
