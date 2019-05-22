<?php

/**
 * 系统仓信息
 */

class Action_Index extends App_Action
{
    private $sGoods;
    private $sCarts;
    private $sUser;
    private $sWarehouse;
    private $sTransfers;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->sGoods = new Service_Goods();
        $this->sCarts = new Service_Carts();
        $this->sUser = new Service_User();
        $this->sInvite = new Service_Invite();
        $this->sWarehouse = new Service_Warehouse();
        $this->sTransfers = new Service_Transfers();
        $this->sReward = new Service_Reward();
        $this->sUser = new Service_User();
        $this->sPromotion = new Service_Promotion();
        $this->sGoodstype = new Service_Goodstype();
        $this->setView(Blue_Action::VIEW_JSON);
    }
    public function __execute()
    {
        $sess=$this->getSession(); 
        $user=$this->sUser->getById($sess['id']);
        if ($this->getRequest()->isGet())
        {
            $this->setView(Blue_Action::VIEW_SMARTY3);
            $type =!empty($_GET['type']) ? intval($_GET['type']) : 0;
            $classid =!empty($_GET['classid']) ? intval($_GET['classid']) : 0;
            $rn =!empty($_GET['rn']) ? intval($_GET['rn']) : 20;
            $pn =!empty($_GET['pn']) ? intval($_GET['pn']) : 1;
            $typeList=$this->sGoodstype->getList(0); 
            foreach($typeList as &$item)
            {
                $item['list']=$this->sGoodstype->getList($item['id']); 
            }
            if(empty($type))
            {
                //云仓进货
                $data = $this->sGoods->getList($classid,$pn,$rn);
                foreach ($data as &$value) {
                    $goodstype=$this->sGoodstype->getById($value['classid']);
                    $value['amount']=$goodstype['user'.$user['reward'].'_price'];
                    //秒杀活动
                    // $promotion=$this->sPromotion->getIfOne($value['id']);
                    // if($promotion['type']==1 && $promotion['num']>=1){
                    //     if($promotion['amount']<$value['amount']){
                    //         $value['ac_amount']=$promotion['amount'];
                    //         $value['stock'] = $promotion['num'];
                    //     }
                    // }
                    // if($promotion['type']==2 || $promotion['type']==3){
                    //     $goodss=  $this->sGoods->getById($promotion['send_gid']);
                    //     $promotion['send_goodsname']=$goodss['goodsname'];
                    //     $value['promotion']=json_encode($promotion);
                    // }
                    // $value['picture']=unserialize($value['photo']);
                    // $value['picture']=implode(',',$value['picture']);
                }
            }
            else
            {
                //上级云仓

                $invite=$this->sInvite->get($sess['id']);
                //判断有没有上级
                if(empty($invite))
                {
                    $this->Warning('没有上级');
                }
                $data=$this->sWarehouse->getById($invite['rid']);
                if($data){
                    foreach($data as $k=>$v)
                    {
                        if($v['goodsid'])
                        {
                            $data[$k]['goods']=  $this->sGoods->getById($v['goodsid']);
                            $data[$k]['picture']=unserialize($data[$k]['goods']['photo']);
                            $data[$k]['picture']=implode(',',$data[$k]['picture']);
                            $data[$k]['sort'] = $data[$k]['goods']['sort'];
                            if($user['reward']>1){
                                $goodstype=$this->sGoodstype->getById($data[$k]['goods']['classid']);
                                $data[$k]['goods']['amount']=$goodstype['user'.$user['reward'].'_price'];
                            }
                            //秒杀活动
                            // $promotion=$this->sPromotion->getIfOne($v['goodsid']);
                            // if($promotion['type']==1 && $promotion['num']>=1){
                            //     if($promotion['amount']<$value['amount']){
                            //         $data[$k]['goods']['ac_amount']=$promotion['amount'];
                            //         $data[$k]['goods']['stock']=$promotion['num'];
                            //     }
                            // }
                            // if($promotion['type']==2 || $promotion['type']==3){
                            //     $goodss=  $this->sGoods->getById($promotion['send_gid']);
                            //     $promotion['send_goodsname']=$goodss['goodsname'];
                            //     $data[$k]['promotion']=json_encode($promotion);
                            // }
                        }
                        $sort[]= $data[$k]['sort'];
                        if(!empty($classid)){
                            if($classid!= $data[$k]['goods']['classid']){
                                 unset($data[$k]);
                            }
                        }
                    }
                    $fromid=$invite['rid'];
                    array_multisort($sort,SORT_ASC,$data);
                }
            }
             //$this->log($data);
            $count= $this->sGoods->getCount($classid);
            $weixin = new App_Weixin();
			$sdk = $weixin->getSDK();
            //获取当前URL
            $url=explode('?',$_SERVER["REQUEST_URI"]); 
            return array('data' => $data,'sdk' => $sdk, 'type'=>$typeList,'fromid'=>$fromid,'page' => Blue_Page::pageInfo($count, $pn, $rn),'url' => $url[0]); 
        }
        else
        {
            $req = $this->verify();
            $amount=0;
            foreach($req['id'] as $k=>$v)
            {   
                if($req['num'][$k]){
                    $goods=$this->sGoods->getById($v);
                    if($goods['stock']<$req['num'][$k]){
                        $this->Warning('商品库存不足');
                    }
                    $goodstype=$this->sGoodstype->getById($goods['classid']);
                    $goods['amount']=$goodstype['user'.$user['reward'].'_price'];
    
    
                    $old_amount+=$goods['amount']*$req['num'][$k];
    
                    //秒杀价格
                    // $promotion=$this->sPromotion->getIfOne($v);
                    // if($promotion){
                    //     //秒杀
                    //     if($promotion['type']==1 && $promotion['num']>=1 && $promotion['amount']<$goods['amount']){
                    //         $goods['amount']=$promotion['amount'];
                    //         $req['pro_id'][$k]=$promotion['id'];
                    //     }
                    //     //买赠（商品和优惠卷）
                    //     if($promotion['num']<=$req['num'][$k]){
                    //         if($promotion['type']==2){
                    //             $goods['amount']-=$promotion['amount'];
                    //             $req['pro_id'][$k]=$promotion['id'];
                    //         }
                    //         if($promotion['type']==3){
                    //             $goods['amount']-=$promotion['amount'];
                    //             $req['pro_id'][$k]=$promotion['id'];
                    //         }
                    //     }
                    // }
                    $amount+=$goods['amount']*$req['num'][$k];
                }
            }

            //判断余额
            $user= $this->sUser->getById($sess['id']);
            if($user['amount']>=$amount)
            {
                $real_amount=0;
            }
            else
            {
                $real_amount=sprintf("%.2f",($amount-$user['amount']));
            }
            
            $ordersn = date('YmdHis') . rand(10000000, 99999999);
            //生成云仓订单
            $ret = array(
                'ordersn' => $ordersn,
                'userid' => $sess['id'],
                'fromid' => $req['fromid'],
                'amount'=> $amount,
                'price' => $old_amount,
                'real_amount'=> $real_amount,//实际支付金额
                'addtime'=>time()
            );
            Blue_Commit::call('transfers_Create', $ret);
            //更新云仓购物车
            $orderData = array(
                'ordersn' => $ordersn,
                'userid' => $sess['id'],
                'id' => $req['id'],
                'num' => $req['num'],
                'pro_id' => $req['pro_id'],
                'amount'=> $amount,
                'addtime'=>time()
            );
            Blue_Commit::call('transfers_Goods', $orderData);
            if(!$req['fromid']){
                //扣除库存
                foreach ($req['id'] as $key => $value) {
                Blue_Commit::call('goods_Stock', array('id'=>$value,'num'=>-$req['num'][$key]));
                }
                  
            }
            //全余额支付
            if($user['amount']>=$amount)
            {
                $transfers = $this->sTransfers->getBySn($ordersn);    
                Blue_Commit::call('transfers_Update', $transfers);   
                
                return array('money' => 0,'ordersn' => $ordersn);
                exit();
            }
            //统一下单调起支付
            $pay = new App_Pay();
            core_log::debug('微信订单信息金额----调拨申请******'.$real_amount);
            

            // $real_amount=0.01;
            $prepay_id = $pay->getPrepayId($sess['openid'], $ordersn,$real_amount*100, 2);
            core_log::debug('微信订单信息金额----调拨申请******'.json_encode($prepay_id));
            return array('orderData' => $orderData, 'prepay_id' => $prepay_id,'money'=>$real_amount*100);
        }   
    }
    public function verify()
    {
        return $_POST;
    }

}
