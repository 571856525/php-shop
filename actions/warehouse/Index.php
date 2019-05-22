<?php

/**
 * 云仓
*/

class Action_Index extends App_Action
{
    private  $sWarehouse;
    private  $sGoods;
    private $sAddress;

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
        $this->setView(Blue_Action::VIEW_JSON);       
    }
    public function __execute()
    {
        $session = $this->getSession();
        $user=$this->sUser->getById($session['id']);
        if ($this->getRequest()->isGet()) {
            $this->setView(Blue_Action::VIEW_SMARTY3);
            $warehouse=$this->sWarehouse->getById($session['id']);
            if($warehouse){
                foreach($warehouse as $k=>$v)
                {   
                    if($v['goodsid'])
                    {
                        $warehouse[$k]['goods']=  $this->sGoods->getById($v['goodsid']);
                        $warehouse[$k]['sort']= $warehouse[$k]['goods']['sort'];

                        $warehouse[$k]['picture']=unserialize($warehouse[$k]['goods']['photo']);
                        $warehouse[$k]['picture']=implode(',',$warehouse[$k]['picture']);
                        if($user['reward']>1){
                            $goodstype=$this->sGoodstype->getById($warehouse[$k]['goods']['classid']);
                            $warehouse[$k]['goods']['amount']=$goodstype['user'.$user['reward'].'_price'];
                        }
                    }
                    $sort[]= $warehouse[$k]['sort'];
                }
                array_multisort($sort,SORT_ASC,$warehouse);
            }
            $address=$this->sAddress->getListByid($session['id']);
            foreach($address as &$value){
                $value['address']=$value['address'].$value['detail'];
            }
            return array('data'=>$warehouse,'address' => $address);
        }
        else
        {
            $req = $this->verify();
            
            //生成订单
            $ordersn = date('YmdHis') . rand(10000000, 99999999);
            //判断商品和收货地址
            if(empty($req['id']) || empty($req['addressid']))
            {
                $this->Warning('商品或收获地址不能为空');
            }
            $goods=array();
            //判断库存
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
                        $goods[]=$data;
                        // $pro=$this->sPromotion->getOne(3,$v,1);
                        // if($pro['num']<=$req['num'][$k]){ //多买赠送活动
                        //     if($pro['send_type']==1){ //增送商品
                        //         $data['num']=$pro['send_num'];
                        //         $data['id']=$pro['send_gid'];
                        //         $goods[]=$data;
                        //     }else{  //赠送优惠卷
                        //         $coupons['send_type']=$pro['send_type'];
                        //         $coupons['send_coupons_name']=$pro['send_coupons_name'];
                        //         $coupons['send_amount']=$pro['send_amount'];
                        //         $coupons['send_least']=$pro['send_least'];
                        //         $coupons['send_full']=$pro['send_full'];
                        //         $coupons['send_reduce']=$pro['send_reduce'];
                        //         $coupons['send_end_time']=$pro['send_end_time'];
                        //     }
                        // }
                    }  
            }
            //订单数据
            $orderData = [
                'userid'=> $session['id'],
                // 'coupons'=>$coupons,
                'ordersn'=> $ordersn,
                'goods'=> json_encode($goods),
                'addressid'=> $req['addressid'],
                'addtime'=> time(),
            ];
            Blue_Commit::call('warehouse_Send', $orderData);
        }
        
    }
    public function verify()
    {
        //取除规格数目为0的
       return $_POST;
    }

}
