<?php

/**
 * 直接购买方式
 */

class Action_Buy extends App_Action
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
        $this->sAddress = new Service_Address();
        $this->sGoodstype = new Service_Goodstype();
        $this->sOrder = new Service_Order();
        $this->sUser = new Service_User();
        $this->sReward = new Service_Reward();
        $this->sCoupons = new Service_Coupons();
        $this->sPromotion= new Service_Promotion();
    }
    public function __execute()
    { 
        $session = $this->getSession();
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY3);
            $id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
            if(empty($id))
            {
                $this->Warning('请选择商品');
            }
            $goods=$this->sGoods->getById($id);
            $list=$this->sAddress->getListByid($session['id']);
            foreach($list as &$value){
                $value['address']=$value['address'].$value['detail'];
            }
            $weixin = new App_Weixin();
            $sdk = $weixin->getSDK();
            $url=explode('?',$_SERVER["REQUEST_URI"]); 
            $user=$this->sUser->getById($session['id']);

            $goodstype=$this->sGoodstype->getById($goods['classid']);

            // $goods['amount']=$goodstype['user'.$user['reward'].'_price'];

            $promotion=$this->sPromotion->getIfOne($id);
            if(!empty($promotion)){
                $is_pro=1;
                if($promotion['type']==1){
                    if($promotion['num']>0){
                        $goods['amount']=$promotion['amount'];
                        $goods['stock']=$promotion['num'];
                    }else{
                        unset($promotion);
                    }
                }
                if($promotion['send_gid']){
                    $shop=$this->sGoods->getById($promotion['send_gid']);
                    $promotion['goodsname'] = $shop['goodsname'];
                }
            }
            if(empty($is_pro)){
                //优惠券设置
                $coupons=$this->sCoupons->getByPay($session['id']);
                if($coupons){
                    foreach($coupons as $key=>$value){
                        if(!empty($value['goods_id'])){
                            if($value['goods_id']!=$id){
                                unset($coupons[$key]);
                                continue;
                            }
                        }
                        //最低消费
                        if($value['full']>$goods['amount']){
                            unset($coupons[$key]);
                            continue;
                        }
                        if($value['type']==0){
                            $sort[$key]=$coupons[$key]['price']=$goods['amount']*$value['money']/100;
                        }
                        if($value['type']==1 && $value['full']<=$goods['amount']){
                            $sort[$key]=$coupons[$key]['price']=$goods['amount']-$value['reduce'];
                        }
                    }
                    //默认最优惠方案
                    array_multisort($sort,SORT_ASC,$coupons);
                }
            }

            return array('goods' => $goods,'list' => $list,'sdk' => $sdk,'url' => $url[0],'coupons'=>$coupons, 'promotion'=>$promotion);
        }
        else
        {
            $session = $this->getSession();
            $req = $this->verify();
            $user_id = $session['openid'];
            //生成订单
            $ordersn = date('YmdHis') . rand(10000000, 99999999);
            
            $goods=$this->sGoods->getById($req['goodsid']);
            if(empty($goods))
            {
                $this->Warning('商品不存在');
            }
            //商品价格处理
            $user=$this->sUser->getById($session['id']);
            //总价         
            $amount=$goods['amount']*$req['num'];
            $promotion=$this->sPromotion->getIfOne($req['goodsid']);
            if(!empty($promotion)){
                //秒杀
                if($promotion['type']==1 && $promotion['num']>0){
                    $goods['amount']=$promotion['amount'];
                    $amount=$goods['amount']*$req['num'];
                    if($req['num']>$promotion['num']){
                        $this->Warning('秒杀商品库存不足'); 
                    }
                }
                //买省
                if($promotion['type']==2){
                    $save=floor($req['num']/$promotion['num'])*$promotion['amount']*$req['num'];
                    $amount-=$save;
                }
                //买赠
                if($promotion['type']==3){
                    $times = floor($req['num']/$promotion['num']);
                }
            }

            // echo $req['cid'];die;
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
            
            //判断库存
            if($req['num']>$goods['stock'])
            {
                $this->Warning('库存不足'); 
            }
            
            //判断余额

            //实际支付金额
            if($user['amount']>=$amount)
            {
                $real_amount=0;
            }
            else
            {
                $real_amount=sprintf("%.2f",($amount-$user['amount']));
                core_log::debug('=--=-=-=------------------'.$amount.'-------------'.$user['amount'].'-------'.$real_amount);
            }
            
            
            //订单数据
            $orderData = [
                'userid'=> $session['id'],
                'ordersn'=> $ordersn,
                'addressid'=> $req['addressid'],
                'amount'=> $amount,
                'real_amount'=> $real_amount,
                'cid'=> $req['cid'],
                'addtime'=> time(),
                'update_time'=> time(),
            ];
            Blue_Commit::call('order_Buy', $orderData);

            //加入购物车
            $cartData = [
                'userid'=> $session['id'],
                'ordersn'=> $ordersn,
                'goodsid'=> $req['goodsid'],
                'num'=> $req['num'],
                'pid'=>$promotion['id'],
                'times'=>$times,
                'addtime'=> time(),
            ];
            Blue_Commit::call('order_Goods', $cartData);
            //全余额支付
            if($real_amount == 0)
            {
                $order = $this->sOrder->getByOrder($ordersn);    
                Blue_Commit::call('order_Update', $order); 
                //扣除库存 
                Blue_Commit::call('goods_Stock', array('id'=>$req['goodsid'],'num'=>(0-$req['num'])));
                return array('money' => 0,'ordersn' => $ordersn);
                exit();
            }
            //扣除库存 
            Blue_Commit::call('goods_Stock', array('id'=>$req['goodsid'],'num'=>(0-$req['num'])));

            //$real_amount=0.01;
            //统一下单调起支付
            $pay = new App_Pay();

            $prepay_id = $pay->getPrepayId($session['openid'], $ordersn, $real_amount*100, 1);
            core_log::debug('微信订单信息金额----直接购买******'.json_encode($prepay_id));
            return array('orderData' => $orderData, 'prepay_id' => $prepay_id,'money'=>$real_amount*100);
        }
    }

    public function verify()
    {
        $rule = array(
            'addressid' => array('filterIntBetweenWithEqual', array(0)),
            'goodsid' => array('filterIntBetweenWithEqual', array(0)),
            'num' => array('filterIntBetweenWithEqual', array(0)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        $req['cid']=$_POST['cid'];
        $req['pid']=$_POST['pid'];
        return $req;
    }

}
