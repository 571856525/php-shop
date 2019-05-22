<?php

/**
 * 上级云仓库存
*/

class Action_Auperior extends App_Action
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
        $this->sReward = new Service_Reward();
        $this->sGoodstype = new Service_Goodstype();
        $this->sPromotion= new Service_Promotion();
        $this->sUser = new Service_User();
        $this->sInvite = new Service_Invite();
        $this->setView(Blue_Action::VIEW_JSON);       
    }
    public function __execute()
    {
        $session = $this->getSession();
        $user=$this->sUser->getById($session['id']);
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY3);
            //上级云仓
            $invite=$this->sInvite->get($session['id']);
            //判断有没有上级
            // if($session['reward']>1 || empty($invite))
            // {
            //     header('Location:/shop/index/index');die;
            // }
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
                        //秒杀活动
                        $promotion=$this->sPromotion->getIfOne($v['goodsid']);
                        if($promotion['type']==1 && $promotion['num']>=1){
                            $data[$k]['goods']['ac_amount']=$data[$k]['goods']['amount']=$promotion['amount'];
                            if($promotion['num']<$v['on_inventory']){
                                $data[$k]['on_inventory']=$promotion['num'];
                            }
                        }
                        if($promotion['type']==2 || $promotion['type']==3){
                            $goodss=  $this->sGoods->getById($promotion['send_gid']);
                            $promotion['send_goodsname']=$goodss['goodsname'];
                            $data[$k]['promotion']=json_encode($promotion);
                        }
                    }
                    $sort[]= $data[$k]['sort'];
                }
                array_multisort($sort,SORT_ASC,$data);
            }
            $address=$this->sAddress->getListByid($session['id']);
            foreach($address as &$value){
                $value['address']=$value['address'].$value['detail'];
            }
            return array('data'=>$data,'list' => $address,'fromid'=>$invite['rid']);
        }
        else
        {
            $req = $this->verify();
            $user=$this->sUser->getById($session['id']);
            //生成订单
            $ordersn = date('YmdHis') . rand(10000000, 99999999);
            //判断商品和收货地址
            if(empty($req['id']) || empty($req['addressid']))
            {
                $this->Warning('商品或收获地址不能为空');
            }
            $goods=array();
            //判断库存
            $amount=0;
            foreach($req['id'] as $k=>$v)
            {
                $ware=$this->sWarehouse->getBywId($v);
                if($ware['on_inventory']<$req['num'][$k])
                {
                    $this->Warning('库存不足!!');
                } 
                if($req['num'][$k]>0){
                    $data['num']=$req['num'][$k];
                    $data['id']=$ware['goodsid'];
                    $goods=$this->sGoods->getById($ware['goodsid']);
                    $price=$goods['amount'];
                    $promotion=$this->sPromotion->getIfOne($ware['goodsid']);
                    if($promotion['type']==1 && $promotion['num']>=1){
                        $price=$promotion['amount'];
                    }
                    $price=$price*$req['num'][$k];
                    if($promotion['type']==2 && $price>=$promotion['full_amount']){
                        //满减
                        $reduce=$promotion['amount'];
                    }else{
                        $reduce=0;
                    }
                    $price-=$reduce;
                    $amount+=$price;
                }  
            }


            if($user['amount']>=$amount)
            {
                $real_amount=0;
            }
            else
            {
                $real_amount=sprintf("%.2f",($amount-$user['amount']));
                core_log::debug('=--=-=-=------------------'.$amount.'-------------'.$user['amount'].'-------'.$real_amount);
            }

            //扣除库存
            // if($req['fromid']){
            //     foreach ($req['id'] as $key => $value) {
            //         Blue_Commit::call('warehouse_Stock', array('id'=>$value,'num'=>-$req['num'][$key]));
            //     }
                  
            // }
            //订单数据
            $orderData = [
                'userid'=> $session['id'],
                'ordersn'=> $ordersn,
                'data'   => json_encode($req), 
                'goods'=> json_encode($goods),
                'addressid'=> $req['addressid'],
                'addtime'=> time(),
            ];
            Blue_Commit::call('order_Createorder', $orderData);
            
            $pay = new App_Pay();
            $prepay_id = $pay->getPrepayId($session['openid'], $ordersn, $real_amount*100, 4);
            core_log::debug('微信订单信息金额----直接购买******'.json_encode($prepay_id));
            return array('orderData' => $orderData, 'prepay_id' => $prepay_id,'money'=>$real_amount*100);
        }
        
    }
    public function verify()
    {
        //取除规格数目为0的
       foreach ($_POST['num'] as $key => $value) {
           if($value==0){
                unset($_POST['num'][$key]);
                unset($_POST['id'][$key]);
           }
       }
       return $_POST;
    }

}
