<?php

/**
 * 订单信息修改
 */

class Commit_Order_Modify extends Blue_Commit
{
    private $dOrder;
    private $dUser;

    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dOrder = new Dao_Order();
        $this->dCart = new Dao_Cart();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $send=$req['send'];
        $pids=$req['pids'];
        unset($req['send']);
        unset($req['pids']);
        $this->dOrder->getById;
        if($req['id']){
            $order=$this->dOrder->getById;
            //未支付
            $cart=$this->dCart->getByOrderId($req['id']);
            foreach ($cart as $key => $value) {
                if($value['is_send']){
                     $is_send=1;
                }
                $this->dCart->update('id='.$value['id'], array('pid'=>$pids[$key]));
            }
            if(empty($is_send)){
                if(empty($send)){
                    $sendData=json_decode($send,true);
                    foreach ($sendData as $key => $v) {
                        $v=json_decode($v,true);
                        $v['orderid']=$order['id'];
                        $this->dCart->insert($v,true);
                        //扣除库存
                        Blue_Commit::call('goods_Stock', array('id'=>$v['goodsid'],'num'=>(0-$v['num'])));
                    }
                }
            }
            $this->dOrder->update(sprintf('id=%d', $req['id']), $req);
        }
        
    }
}
