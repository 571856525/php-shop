<?php

/**
 * 云仓发货
*/

class Action_Update extends App_Action
{
    private  $sWarehouse;
    private  $sGoods;
    private  $sAddress;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->sWarehouse = new Service_Warehouse();
        $this->sGoods = new Service_Goods();
        $this->sAddress = new Service_Address();
        $this->setView(Blue_Action::VIEW_JSON);
    }

    public function __execute()
    {

        $sess=$this->getSession(); 
        if ($this->getRequest()->isGet()) {
            $session = $this->getSession();
            $id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
            if(empty($id))
            {
                $this->Warning('请选择商品');
            }
            $this->setView(Blue_Action::VIEW_SMARTY3);
            $goods=$this->sGoods->getById($id);
            $list=$this->sAddress->getListByid($session['id']);
            return array('goods' => $goods,'list' => $list);
        } 
        else
        {

           
            $req=$this->verify();
            $ret = array(
                'userid' => $sess['id'],
                'on_inventory' => $req['on_inventory'],
                're_inventory' => $req['re_inventory']
            );
            Blue_Commit::call('warehouse_Update', $ret);
            return $ret;
        }
    }
    public function verify()
    {
        $rule = array(
            'on_inventory' =>array('filterIntBetweenWithEqual',array(0)),//
            're_inventory' =>array('filterIntBetweenWithEqual',array(0))
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }
    
}
