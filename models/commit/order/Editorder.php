<?php

/**
 * 支付订单修改
 */

class Commit_Order_Editorder extends Blue_Commit
{
    private $dOrder;
    private $dCart;
    private $dUser;
    private $dRecord;
    private $dInvite;

    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dOrder = new Dao_Order();
        $this->dCart = new Dao_Cart();
        $this->dUser = new Dao_User();
        $this->dRecord = new Dao_Record();
        $this->dInvite = new Dao_Invite();
        $this->dCoupons = new Dao_Coupons();
        $this->dPromotion= new Dao_Promotion();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $order = $this->dOrder->getById($req['id']); 
        $user= $this->dUser->getById($order['userid']);
        //修改订单状态
        Core_log::Warning('直购支付接口回调数据---------->'.json_encode($req));
        $this->dOrder->update('id='.$req['id'], array('state'=>1,'update_time'=>time()));

        if(!empty($order['cid'])){
            $this->dCoupons->update('id='.$order['cid'],'status=2');
        }


        //秒杀减少库存
        $cart =$this->dCart->getByOrderId($req['id']);
        foreach($cart as $v)
        {
            if($v['pid']){
                $promotion=$this->dPromotion->get($v['pid']); 
                if($promotion['type']==1){
                    $this->dPromotion->update('id='.$v['pid'],array('num'=>($promotion['num']-$v['num'])));
                }
                //赠送优惠卷
                if($promotion['type']==3){
                    $times=floor($v['num']/$promotion['num']);
                     if($promotion['send_type']>1){
                        //赠送优惠卷
                        if($promotion['send_type']==2){  //赠送折扣卷
                            $data=array(
                                'userid' => $order['userid'],
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
                            $data=array(
                                'userid' => $order['userid'],
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
                        for($i=0;$i<$times;$i++){
                            $this->dCoupons->insert($data, true);
                        }
                     }
                   
                }
            }       
        }
        $lama = new App_Lama();
        //写入操作记录
        if(!empty($order['userid']))
        {
            //收支记录
            $data=array(
                'userid'=>$order['userid'],
                'amount'=>$order['amount'],
                'ordersn'=>$order['ordersn'],
                'type'=>1,
                'content'=>'您购买商品花费：'.$order['amount'].',单号：'.$order['ordersn'],
                'addtime' => time()
            );
            $this->dRecord->insert($data, true);
        }
        //判断是否扣除余额
        if($order['real_amount']<$order['amount'])
        {
            //扣除余额数
            $amount=$order['amount']-$order['real_amount'];
            //扣除余额
            $this->dUser->update('id='.$order['userid'], array('amount'=>$user['amount']-$amount));
            //收支记录
            $record=$this->dRecord->selectOne("ordersn='".$order['ordersn']."'",'id');
            if($order['real_amount']!=0){
                $this->dRecord->update('id='.$record['id'],array('content'=>'您购买商品花费：'.$order['amount'].'元,系统余额扣除'.($order['amount']-$order['real_amount']).'元,微信支付扣除'.$order['real_amount'].'元,单号：'.$order['ordersn']));
            }else{
                $this->dRecord->update('id='.$record['id'],array('content'=>'您购买商品花费：'.$order['amount'].'元,系统余额扣除'.$order['amount'].'元,单号：'.$order['ordersn']));
            }
        }
        
        if($order['real_amount']==0){
            $keyword='系统余额支付';
        }else if($order['real_amount']==$order['amount']){
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
                'first'    => array('value' => '您好，您的商城订单已付款成功'),
                'keyword1' => array('value' => $order['ordersn']),
                'keyword2' => array('value' => date('Y-m-d H:i:s',$order['addtime'])),
                'keyword3' => array('value' => $order['amount']),
                'keyword4' => array('value' =>  $keyword),
                'remark'   => array('value' => '感谢您的惠顾!!!'),
            )
        );
        $json_1 = json_encode($data_1);
        $da1 = $weixin->setNotice($json_1);
        //获取订单货物总数
        $num =$lama->getOrderCount($order['ordersn']);
        Core_log::debug('订单数量---------->'.$num);
 

        //月度销售量增加
		//更新月度销量
        //获取订单下商品
        // $cart =$this->dCart->getByOrderId($req['id']);
        // foreach($cart as $v)
        // {
        //     //更新业绩
        //     $lama->changeSales($v['userid'],$v['num'],$v['amount']);
        //     //更新商品销量
        //     $lama->changeStatic($v['goodsid'],$v['num']);
        // }
        
        //永久奖励
        $invite=$this->dInvite->getById($order['userid']);
        $top=$this->dUser->getById($invite['rid']);
        $user=$this->dUser->getById($order['userid']);
        if($user['reward']>1 && $top['reward']==$user['reward']){
            $lama->changeReward($user['id'],$top['id'],$num,$order['ordersn']);
        }
    }
}
