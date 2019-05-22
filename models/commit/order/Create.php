<?php

/**
 * 支付订单创建
 */

class Commit_Order_Create extends Blue_Commit
{
    private $dOrder;
    private $dCart;

    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dOrder = new Dao_Order();
        $this->dCart = new Dao_Cart();
        $this->dGoods= new Dao_Goods();
        $this->dCombination= new Dao_Combination();
        $this->dPromotion= new Dao_Promotion();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $num=$req['num'];
        $ids=$req['id'];
        $send=$req['send'];
        $pids=$req['pids'];
        unset($req['num']);
        unset($req['id']);
        unset($req['send']);
        unset($req['pids']);

        $this->dOrder->insert($req, true);
        
        //更新购物车
        $order = $this->dOrder->getByOrder($req['ordersn']);
        if($order)
        {   
            if(empty($req['comid'])){
                foreach ($ids as $key => $value) {
                    $this->dCart->update('id='.$value, array('orderid'=>$order['id'],'pid'=>$pids[$key]));
                }
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
            }else{
                //组合套餐支付
                $combination=$this->dCombination->get($req['comid']);
                $num=($num>=2)?$num:1;
                if($combination['goods_id1']){
                     $goods=$this->dGoods->getById($combination['goods_id1']);
                     $cart1=array(
                        'userid' => $order['userid'],
                        'orderid' => $order['id'],
                        'goodsid' => $combination['goods_id1'],
                        'num' => $num,
                        'addtime' => time(),
                     );
                     $this->dCart->insert($cart1, true);
                }
                if($combination['goods_id2']){
                    $goods=$this->dGoods->getById($combination['goods_id2']);
                    $cart2=array(
                       'userid' => $order['userid'],
                       'orderid' => $order['id'],
                       'goodsid' => $combination['goods_id2'],
                       'num' => $num,
                       'addtime' => time(),
                    );
                    $this->dCart->insert($cart2, true);
               }
               if($combination['goods_id3']){
                    $goods=$this->dGoods->getById($combination['goods_id3']);
                    $cart3=array(
                        'userid' => $order['userid'],
                        'orderid' => $order['id'],
                        'goodsid' => $combination['goods_id3'],
                        'num' => $num,
                        'addtime' => time(),
                    );
                    $this->dCart->insert($cart3, true);
               }
               if($combination['goods_id4']){
                    $goods=$this->dGoods->getById($combination['goods_id4']);
                    $cart4=array(
                        'userid' => $order['userid'],
                        'orderid' => $order['id'],
                        'goodsid' => $combination['goods_id4'],
                        'num' => $num,
                        'addtime' => time(),
                    );
                    $this->dCart->insert($cart4, true);
                }
                if($combination['goods_id5']){
                    $goods=$this->dGoods->getById($combination['goods_id5']);
                    $cart5=array(
                        'userid' => $order['userid'],
                        'orderid' => $order['id'],
                        'goodsid' => $combination['goods_id5'],
                        'num' => $num,
                        'addtime' => time(),
                    );
                    $this->dCart->insert($cart5, true);
                }
            } 
        }
    }
}
