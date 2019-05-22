<?php

/**
 * 订单详情
 */

class Action_Apply extends App_Action
{

    private $sReturns;
    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sOrder = new Service_Order();

    }

    public function __execute()
    {
        $ret=$this->verify();
        $sess=$this->getSession();
        $id=$ret['id'];
        if(empty($id)){
            $this->Warning('订单号不能为空');
        }
        $order=$this->sOrder->getById($id);
        if(empty($order)){
            $this->Warning('订单号不存在');
        }
        if($order['state']==0){
            $this->Warning('订单号未支付');
        }  

        if($order['state']==1 && $order['state']==2){//仅退款
            $type=1;
        }else if($order['state']==3){//退款和退货

            $type=2;
        }
        $data=array(
             'orderid'=>$ret['id'],
             'userid'=>$sess['id'],
             'realname'=>$ret['realname'],
             'phone'=>$ret['phone'],
             'reason'=>$ret['reason'],
             'describe'=>$ret['describe'],
             'status'=>0,
             'type'  =>$type,
             'create_time'=>time(),
             'update_time'=>time()
        );
        Blue_Commit::call('returns_Create',$data);
        return array('data'=>$data);
    }
    public function verify()
    {
        $rule = array(
            'id' => array('filterIntBetweenWithEqual', array(0)),
            'realname' => array('filterStrlen', array(1, 10)),
            'phone' => array('filterStrlen', array(1, 11)),
            'reason' => array('filterStrlen', array(1, 10)),
            'describe' => array('filterStrlen', array(1, 50)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }
}
