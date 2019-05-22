<?php

/**
 * 调拨支付订单修改
 */

class Commit_Transfers_Update extends Blue_Commit
{
    private $dTransfers;
    private $dCart;
    private $dUser;
    private $dRecord;

    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dTransfers = new Dao_Transfers();
        $this->dCarts = new Dao_Carts();
        $this->dUser = new Dao_User();
        $this->dRecord = new Dao_Record();
        $this->dPromotion = new Dao_Promotion();
        $this->dCoupons   = new Dao_Coupons();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $transfers = $this->dTransfers->getById($req['id']); 
        $user= $this->dUser->getById($transfers['userid']);
        //修改订单状态
        Core_log::Warning('调拨支付接口回调数据---------->'.json_encode($req));
        $this->dTransfers->update('id='.$req['id'], array('state'=>1));


        $lama = new App_Lama();
        //写入操作记录
        if(!empty($transfers['userid']))
        {           
            //上级通知
            if(!empty($transfers['fromid']))
            {
                $from=$this->dUser->getById($transfers['fromid']);
                $data=array(
                    'userid'=>$transfers['fromid'],
                    'content'=>'下级用户申请调拨商品，调拨单号：'.$transfers['ordersn'],
                    'addtime'=>time(),
                 );
                 $lama->insertLog($data);
            }
            //我的通知
            $data=array(
                'userid'=>$order['userid'],
                'content'=>'申请调拨商品，调拨单号：'.$transfers['ordersn'],
                'addtime'=>time(),
            );
           
            
            $lama->insertLog($data);

            if($transfers['real_amount']==0){
                $keyword='系统余额支付';
            }else if($transfers['real_amount']==$transfers['amount']){
                $keyword='系统余额支付';
            }else{
                $keyword='系统余额+微信支付';
            }
            $weixin = new App_Weixin();
            $temp = Arch_Yaml::get('template');	
            $data_1 = array(
                'touser' => (string)$user['openid'],      
                'template_id' => $temp['order']['tid1'],     //模版id 
                'data' => array(
                    'first' => array('value' => '您好，您的云仓订单已付款成功'),
                    'keyword1' => array('value' => $transfers['ordersn']),
                    'keyword2' => array('value' => date('Y-m-d H:i:s',$transfers['addtime'])),
                    'keyword3' => array('value' => $transfers['amount']),
                    'keyword4' => array('value' =>  $keyword),
                    'remark' => array('value' => '感谢您的惠顾!!!'),
                )
            );
            $json_1 = json_encode($data_1);
            $da1 = $weixin->setNotice($json_1);

            //通知上级
            $url='http://'.$_SERVER['HTTP_HOST']."/shop/transfers/purchase?ctype=3&type=2";
            $content="你好，你的下级".$user['nickname']."向你申请调拨商品"."\n".
                    "---------------------"."\n".
                    "点击"."<a href='".$url."'>下级订单</a>进行下拨商品！！";
            $weixin->pushMsg($from['openid'],'text', $content);
        }
        //判断是否扣除余额
        if($transfers['real_amount']<$transfers['amount'])
        {
            $user= $this->dUser->getById($transfers['userid']);
            //扣除的余额数
            $amount=$transfers['amount']-$transfers['real_amount'];
            //扣除余额
            $this->dUser->update('id='.$transfers['userid'], array('amount'=>$user['amount']-$amount));
            //记录
            $data=array(
                'userid'=>$transfers['userid'],
                'amount'=>$amount,
                'ordersn'=>$transfers['ordersn'],
                'type'=>2,
                'addtime' => time()
            );
            if($transfers['fromid']){
                $data['content']='您从上级调拨商品,支付'.$transfers['amount'].'元，扣除系统余额：'.$amount.'元,单号：'.$transfers['ordersn'];
            }else{
                $data['content']='您从云仓调拨商品,支付'.$transfers['amount'].'元，扣除系统余额：'.$amount.'元,单号：'.$transfers['ordersn'];
            }
            $this->dRecord->insert($data, true);
            if($transfers['fromid']){
                $data=array(
                    'userid'=>$transfers['fromid'],
                    'subid' => $transfers['userid'],
                    'amount'=>$amount,
                    'ordersn'=>$transfers['ordersn'],
                    'content'=>'你的下级申请调拨商品,已支付'.$transfers['amount'].'元，扣除系统余额：'.$amount.'元,单号：'.$transfers['ordersn'].",赶紧去<a href='/shop/transfers/purchase?ctype=3&type=2&audit=0'>云仓订单</a>审核吧！",
                    'type'=>2,
                    'addtime' => time()
                );
                $this->dRecord->insert($data, true);
            }
        }
        

        //买赠送优惠卷活动
        $carts =$this->dCarts->getByOrderId($req['id']);
        foreach($carts as $key=>$value){
            
            if($value['pro_id']){
                $promotion=$this->dPromotion->get($value['pro_id']);
                if($promotion['type']==3){
                    if($promotion['send_type']>=1){
                        $times=floor($value['num']/$promotion['num']);
                        if($promotion['send_type']==2){  //赠送折扣卷
                            $data2=array(
                                'userid' => $transfers['userid'],
                                'money'  => $promotion['send_amount'],
                                'status' => 1,
                                'create_time'=> TIMESTAMP,
                                'end_time' => $promotion['send_end_time'],
                                'type'     =>  0,
                                'logo'     => $promotion['title'],
                                'title'    => $promotion['send_coupons_name']
                            );
                        }
                        if($promotion['send_type']==3){  //赠送满减卷
                            $data2=array(
                                'userid' => $transfers['userid'],
                                'status' => 1,
                                'create_time'=> TIMESTAMP,
                                'end_time' => $promotion['send_end_time'],
                                'type'     =>  1,
                                'logo'     => $promotion['title'],
                                'title'    => $promotion['title'],
                                'full'     => $promotion['send_full'],
                                'reduce'     => $promotion['send_reduce'],
                                'title'    => $promotion['send_coupons_name']
                            );
                        }
                        if($data2){
                            for($i=0;$i<$times;$i++){
                                $this->dCoupons->insert($data2, true);
                            }
                        }
                        
                    }
                }
            }
        }

        //系统云仓调拨
        if(empty($transfers['fromid']))
        {
            //获取订单下商品
            $carts =$this->dCarts->getByOrderId($req['id']);
            foreach($carts as $v)
            {
                if($v['is_send']){  
                    //赠送的商品直接扣除平台的库存,并不在销售额里面算
                    //因为是赠送的商品，所以不需要计算销量
                    $lama->changeWare($v['userid'], $v['goodsid'],$v['num'],$v['amount'],0);
                    Blue_Commit::call('goods_Stock', array('id'=>$v['goodsid'],'num'=>-$v['num']));
                }else{
                    //更新月度销量
                    //更新库存及月度销量
                    $lama->changeWare($v['userid'], $v['goodsid'],$v['num'],$v['amount'],1);
                }
                $lama->changeStatic($v['goodsid'],$v['num']);
                
            }  
        }else{
            //获取订单下商品
            $carts =$this->dCarts->getByOrderId($req['id']);
            foreach($carts as $v)
            {
                if($v['is_send']){  //赠送的商品直接扣除平台的库存,并不在销售额里面算
                    Blue_Commit::call('goods_Stock', array('id'=>$v['goodsid'],'num'=>-$v['num']));
                }else{
                    //更新上级库存
                    $lama->changeWare($transfers['fromid'], $v['goodsid'],-$v['num'],$v['amount'],0);
                }
            }  
        }
    }
}
