<?php

/**
 * 购物车购买方式
 */

class Action_Create extends App_Action
{

    private $sGoods;
    private $sCart;
    private $sOrder;
    private $sAddress;
    private $sUser;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_JSON);
        $this->sGoods = new Service_Goods();
        $this->sCart = new Service_Cart();
        $this->sOrder = new Service_Order();
        $this->sAddress = new Service_Address();
        $this->sUser = new Service_User();
        $this->sReward = new Service_Reward();
        $this->sGoodstype = new Service_Goodstype();
        $this->sCoupons = new Service_Coupons();
        $this->sPromotion= new Service_Promotion();
        $this->sCombination= new Service_Combination();
    }

    public function __execute()
    {
        $session = $this->getSession();
        $user= $this->sUser->getById($session['id']);
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY3);
            $id = !empty($_GET['id']) ? trim($_GET['id']) : '';
            $type = !empty($_GET['type']) ? trim($_GET['type']) :0; #搭配商品购买
            $user=$this->sUser->getById($session['id']);
            $list=$this->sAddress->getListByid($session['id']);
            foreach($list as &$value){
                $value['address']=$value['address'].$value['detail'];
            }
            $weixin = new App_Weixin();
            $sdk = $weixin->getSDK();
            $url=explode('?',$_SERVER["REQUEST_URI"]);
            if(empty($type)){
                //非搭配商品购买
                if(empty($id))
                {
                    $this->Warning('商品信息不能为空');
                }
                else
                {
                    $id=array_filter(explode(',',$id));
                } 
                $cart=$this->sCart->getById(implode(',',$id));
                foreach($cart as &$v)
                {
                    $v['goods']=$this->sGoods->getById($v['goodsid']);
                    $v['amount_old']=$v['amount'];
                    if($v['is_send']==0){
                        //属于秒杀商品的
                        $promotion=$this->sPromotion->getIfOne($v['goodsid']);
                        if($promotion){
                            $state=1;
                        }
                        if($promotion['type']==1){
                            //秒杀
                            if(!empty($promotion) && $promotion['num']>0){
                                $v['goods']['amount']=$promotion['amount'];
                                $v['goods']['stock']=$promotion['num'];
                            }  
                        }
                        $prize=$v['goods']['amount']*$v['num'];
                        if($prize>=$promotion['amount']){
                            //满减
                            $v['amount'] = $prize-$promotion['amount'];
                            $v['remark'] ="满".(int)$promotion['full_amount']."件,减".(int)$promotion['amount']."元";
                        }
                        if($promotion['type']==3){
                            //买赠活动
                            if($v['num']>=$promotion['num']){
                                $times=floor($v['num']/$promotion['num']);
                                if($promotion['send_type']==1){
                                    //赠送商品
                                    $goods=$this->sGoods->getInfo($promotion['send_gid']);
                                    $v['remark'] ="买".$v['num']."件，送".$goods['goodsname']." ".($times*$promotion['send_num'])."件";
                                }else{
                                    //赠送优惠卷
                                    $v['remark'] ="买".$v['num']."件，送".$promotion['send_coupons_name'].$times."个";
                                }
                                
                            }
                        }        
                    }
                    $amount+=$v['amount'];
                    $amount_old+=$v['amount_old'];
                }

                if(!empty($_GET['orderid'])){
                    $info=$this->sOrder->getById($_GET['orderid']);
                    if($info['is_edit']==1){
                        //表示该订单后台修改过金额
                        $amount=$info['amount'];
                    }  
                    //使用优惠券未支付自动删除，重新选择
                    if(!empty($info['cid'])){
                        Blue_Commit::call('order_Delete',array('id'=>$info['id']));
                        header('location:/shop/index/index');
                    }
                }
            
                if($info['is_edit']!=1){  
                    //商品不存在促销或者秒杀时，返回优惠券
                    if(empty($state)){
                        //优惠券设置
                        $coupons=$this->sCoupons->getByPay($session['id']);
                        if($coupons){
                            foreach($coupons as $key=>$value){
                                
                                if($value['full']>$amount){
                                    unset($coupons[$key]);
                                    continue;
                                }
                                //存在着某张商品优惠券
                                if(!empty($value['goods_id'])){
                                    if(!in_array($value['goods_id'],$id)){
                                        unset($coupons[$key]);
                                        continue;
                                    }
                                }
                                if($value['type']==0){
                                    $sort[$key]=$coupons[$key]['price']=$amount*$value['money']/100;
                                }
                                if($value['type']==1 && $value['full']<=$amount){
                                    $sort[$key]=$coupons[$key]['price']=$amount-$value['reduce'];
                                }
                            }
                            //默认最优惠方案
                            if(is_array($sort)){
                                array_multisort($sort,SORT_ASC,$coupons);
                            }  
                        }
                    }
                }
            }else{
                //搭配商品购买
                $combination=$this->sCombination->get($id);
                if(empty($combination)){
                     $this->Warning('该组合套餐不存在或已关闭');
                }
                if(!empty($combination)){
                   //几个商品搭配
                   if($combination['goods_id1']){
                        $goods=$this->sGoods->getById($combination['goods_id1']);
                        $cart1=array(
                            "goods"=>$goods,
                            'num'  =>1
                        );
                        $cart[]=$cart1;
                    }  
                    if($combination['goods_id2']){
                        $goods=$this->sGoods->getById($combination['goods_id2']);
                        $cart2=array(
                            "goods"=>$goods,
                            'num'  =>1
                        );
                            $cart[]=$cart2;
                    }
                    if($combination['goods_id3']){
                        $goods=$this->sGoods->getById($combination['goods_id3']);
                        $cart3=array(
                            "goods"=>$goods,
                            'num'  =>1
                        );
                        $cart[]=$cart3;
                    }
                    if($combination['goods_id4']){
                        $goods=$this->sGoods->getById($combination['goods_id4']);
                        $cart4=array(
                            "goods"=>$goods,
                            'num'  =>1
                        );
                        $cart[]=$cart4;
                    }
                    if($combination['goods_id5']){
                        $goods=$this->sGoods->getById($combination['goods_id5']);
                        $cart5=array(
                            "goods"=>$goods,
                            'num'  =>1
                        );
                        $cart[]=$cart5;
                    }
                    $amount=$combination['amount'];
                }
            }
            // $this->log(array('cart' => $cart,'sdk' => $sdk,'list' => $list,'url' => $url[0],'amount'=>$amount,'coupons'=>$coupons,'comid'=>$combination['id']));
            return array('amount_old'=>$amount_old,'type'=>$_GET['type'], 'cart' => $cart, 'sdk' => $sdk, 'list' => $list, 'url' => $url[0], 'amount'=>$amount, 'coupons'=>$coupons, 'comid'=>$combination['id']);
        }
        else
        {   
            $req = $this->verify();
            // $fp = fopen("lock.txt", "w+");
            //生成订单
            $ordersn = date('YmdHis') . rand(10000000, 99999999);
            //总价
            if(empty($req['id']) || empty($req['address']))
            {
                $this->Warning('商品或收获地址不能为空');
            }
            if(empty($req['comid'])){
                //非组合套餐使用
                $amount=0;
                $cart=$this->sCart->getById(implode(',',$req['id']));
                $pids=array();
                foreach($cart as $k=>$v)
                {
                    $senddata=null;
                    $goods=$this->sGoods->getById($v['goodsid']);
                    //会员价格
                    // if($user['reward']>1){
                    //     $goodstype=$this->sGoodstype->getById($goods['classid']);
                    //     $goods['amount']=$goodstype['user'.$user['reward'].'_price'];
                    // }
                    if($goods)
                    {
                        //判断库存
                        if($v['num']>$goods['stock'])
                        {
                            $this->Warning('库存不足'); 
                        }
                        
                        if($v['is_send']==0){
                            $promotion=$this->sPromotion->getIfOne($v['goodsid']);
                            if($promotion){
                                //属于秒杀商品的
                                if($promotion['type']==1 && $promotion['num']>0){
                                    $goods['amount']=$promotion['amount'];
                                    if($v['num']>$promotion['num']){
                                        $this->Warning('秒杀商品库存不足'); 
                                    }
                                }

                                //属于多买省
                                if($promotion['type']==2 && $promotion['full_amount']<=$v['amount']){
                                    //属于减价的
                                    $is_save=1;
                                    $save_money=$promotion['amount'];

                                }

                                //属于买赠活动
                                if($promotion['type']==3 && $promotion['num']<=$v['num']){
                                    
                                    $pro_id=$promotion['id'];
                                    if($promotion['send_type']==1){
                                        //赠送商品
                                        $senddata=json_encode(array(
                                            'userid' => $session['id'],
                                            'goodsid'=> $promotion['send_gid'],
                                            'num'    => (floor($v['num']/$promotion['num'])*$promotion['send_num']),
                                            'pid'    => $promotion['pid'],
                                            'is_send'=>1,
                                            'addtime'=>time()
                                        ));
                                    }else{
                                        //赠送优惠卷
                                        $pids[$k] = $promotion['id']; 
                                    }
                                }
                            }else{
                                $pids[$k] = 0;
                            }


                            if($senddata){
                                $sendDate[$k]=$senddata;
                            }

                            if($is_save){
                                $amount=$amount+$goods['amount']*$v['num']-$save_money;
                            }else{
                                $amount=$amount+$goods['amount']*$v['num'];
                            }   
                        }
                          
                    } 
                }
                 //优惠券使用
                if(!empty($req['cid'])){
                     $coupons=$this->sCoupons->get($req['cid']);
                     if($coupons['type']==0){
                         $amount*=$coupons['money']/100;
                     }
                     if($coupons['type']==1){
                          if($amount>=$coupons['full']){
                             $amount-=$coupons['reduce'];
                          }
                     }
                }
            }else{
                //组合套餐使用
                $combination=$this->sCombination->get($req['comid']);
                if(empty($combination)){
                    $this->Warning('该组合套餐不存在或已关闭');
                }
                //购物车拼接
                $num=($req['num']>=2)?$num:1;
                if($combination['goods_id1']){
                    $goods=$this->sGoods->getById($combination['goods_id1']);
                    $cart1=array(
                        "goodsid"=>$goods['id'],
                        'num'  =>$num
                    );
                    //判断库存
                    if($num>$goods['stock'])
                    {
                        $this->Warning('库存不足'); 
                    }
                    $cart[]=$cart1;
                }
                if($combination['goods_id2']){
                   $goods=$this->sGoods->getById($combination['goods_id2']);
                   $cart2=array(
                       "goodsid"=>$goods['id'],
                       'num'  =>$num
                   );
                   if($num>$goods['stock'])
                   {
                       $this->Warning('库存不足'); 
                   }
                   $cart[]=$cart2;
                }
                if($combination['goods_id3']){
                   $goods=$this->sGoods->getById($combination['goods_id3']);
                   $cart3=array(
                       "goodsid"=>$goods['id'],
                       'num'  =>$num
                   );
                   if($num>$goods['stock'])
                   {
                       $this->Warning('库存不足'); 
                   }
                   $cart[]=$cart3;
                }
                if($combination['goods_id4']){
                   $goods=$this->sGoods->getById($combination['goods_id4']);
                   $cart4=array(
                       "goodsid"=>$goods['id'],
                       'num'  =>$num
                   );
                   if($num>$goods['stock'])
                   {
                       $this->Warning('库存不足'); 
                   }
                   $cart[]=$cart4;
                }
                if($combination['goods_id5']){
                   $goods=$this->sGoods->getById($combination['goods_id5']);
                   $cart5=array(
                       "goodsid"=>$goods['id'],
                       'num'  =>$num
                   );
                   if($num>$goods['stock'])
                   {
                       $this->Warning('库存不足'); 
                   }
                   $cart[]=$cart5;
                }
                $amount=$combination['amount'];
            }
            //实际支付金额
            if($user['amount']>=$amount)
            {
                $real_amount=0;
            }
            else
            {
                $real_amount=sprintf("%.2f",($amount-$user['amount']));
            }

            //订单数据
            if(!empty($req['unpaid'])){
                //修改未支付的订单
                $order=$this->sOrder->getById(intval($req['orderid']));
                //如何修改过的订单，金额也就变了。
                if($order['is_edit']==1){
                     $amount=$order['amount'];
                }
                //实际支付金额
                if($user['amount']>=$amount)
                {
                    $real_amount=0;
                }
                else
                {
                    $real_amount=$amount-$user['amount'];
                }
                $orderData = [
                    'id'    => $order['id'],  
                    'addressid'=> $req['address'],
                    'amount'=> $amount,
                    'real_amount'=> $real_amount,
                    'update_time'=> time(),
                    'send'=>json_encode($sendDate),
                    'pids'=>$pids
                ];
                $ordersn=$order['ordersn'];
                Blue_Commit::call('order_Modify', $orderData);
            }else{
                //产生新的订单
                 $orderData = [
                    'userid'=> $session['id'],
                    'ordersn'=> $ordersn,
                    'addressid'=> $req['address'],
                    'amount'=> $amount,
                    'real_amount'=> $real_amount,
                    'addtime'=> time(),
                    'update_time'=> time(),
                    'cid'=> $req['cid'],
                    'comid'=>$req['comid'],
                    'num'=>$req['num'],
                    'id'=>$req['id'],
                    'send'=>json_encode($sendDate),
                    'pids'=>$pids
                ];
                Blue_Commit::call('order_Create', $orderData);
            }
            // if($order['is_edit']!=1){//不存在金额修改时，才能修改数量。
                //修改购物车信息
                // $goodsdata = [
                //     'ordersn'=> $ordersn,
                //     'id'=> $req['id'],
                //     'num'=> $req['num'],
                //     'userid'=>$session['id'],
                // ];
                // Blue_Commit::call('order_Change', $goodsdata);
            // }

            //扣除库存 
            foreach($cart as $k=>$v)
            {
                Blue_Commit::call('goods_Stock', array('id'=>$v['goodsid'],'num'=>(0-$v['num'])));
                
            }

            //全余额支付
            if($real_amount == 0)
            {
                
                $order = $this->sOrder->getByOrder($ordersn);    
                Blue_Commit::call('order_Update', $order);
                return array('money' => 0,'ordersn' => $ordersn);
                exit();
            }
            //购买
            // $real_amount=0.01;
            $pay = new App_Pay();
            core_log::debug('微信订');
            $prepay_id = $pay->getPrepayId($session['openid'], $ordersn, $real_amount*100, 1);
            core_log::debug('微信订单信息金额----直接购买******'.json_encode($prepay_id));
            return array('orderData' => $orderData, 'prepay_id' => $prepay_id,'money'=>$real_amount*100);
        }
    }
    public function verify()
    {
        return $_POST;
    }

}
