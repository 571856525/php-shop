<?php

/**
 * 订单商品创建
 */

class Commit_Order_Goods extends Blue_Commit
{
    private $dCart;
    private $sOrder;
    private $dGoods;
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dCart = new Dao_Cart();
        $this->dOrder = new Dao_Order();
        $this->dGoods = new Dao_Goods();
        $this->dUser = new Dao_User();
        $this->dCoupons = new Dao_Coupons();
        $this->dPromotion= new Dao_Promotion();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        if($req)
        {
            $order = $this->dOrder->getByOrder($req['ordersn']);
            
            $times=$req['times'];
            $pid =$req['pid'];
            if($order)
            {   
                //获取商品信息
                $sg=$this->dGoods->getById($req['goodsid']);
                $amount=$sg['amount'];
                // $user=$this->dUser->getById($orderid['userid']);
              
                
                $cartData = array(
                    'userid'  => $order['userid'],
                    'orderid' => $order['id'],
                    'goodsid' => $req['goodsid'],
                    'num'     => $req['num'],
                    'amount'  => $order['amount'],
                    'pid'     => $pid,
                    'addtime' => time()
                );
                $this->dCart->insert($cartData, true);

                if($times){
                    //赠送
                    $pro=$this->dPromotion->get($pid);
                    if($pro['send_type']==1){
                        //赠送商品
                        $cartData = array(
                            'userid' => $order['userid'],
                            'orderid' => $order['id'],
                            'goodsid' => $pro['send_gid'],
                            'num' => ($times*$pro['send_num']),
                            'is_send' => 1,
                            'addtime' => time(),
                        );
                        $this->dCart->insert($cartData, true);
                        //扣除库存
                        Blue_Commit::call('goods_Stock', array('id'=>$cartData['goodsid'],'num'=>(0-$cartData['num'])));
                    }else{
                        
                    }
                }
            }

        }
    }
}
