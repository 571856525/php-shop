<?php

/**
 * 订单确认收货
 */

class Commit_Order_Confirm extends Blue_Commit
{
    private $dOrder;
    

    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dOrder = new Dao_Order();
        $this->dUser = new Dao_User();
        $this->dRecord = new Dao_Record();
        $this->dCart = new Dao_Cart();
        $this->dAddress = new Dao_Address();
        $this->dRewardtemp = new Dao_Rewardtemp();
        $this->weixin = new App_Weixin();
   
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        //修改订单状态
        $this->dOrder->update('id='.$req['id'], array('state'=>3,'update_time'=>time()));
        $order=$this->dOrder->getById($req['id']);
        $user=$this->dUser->getById($order['userid']);
        $address=$this->dAddress->getById($order['addressid']);
         //模板消息
        $weixin = new App_Weixin();
        $temp = Arch_Yaml::get('template');	
        $data_1 = array(
             'touser' => (string)$user['openid'],      
             'template_id' => $temp['order']['tid3'],     //模版id 
             'data' => array(
                 'first' => array('value' => '你已确认收货！！！'),
                 'keyword1' => array('value' => $address['address']),
                 'keyword2' => array('value' => $address['contact']),
                 'keyword3' => array('value' => $address['mobile']),
                 'remark' => array('value' => '感谢你的订购。'),
             )
         );
        $json_1 = json_encode($data_1);
        $da1 = $weixin->setNotice($json_1);

        //确定收货后，更新月度奖励
        $lama=new App_Lama();
        $cart =$this->dCart->getByOrderId($req['id']);
        foreach($cart as $v)
        {
            //更新上级月度奖励
            $lama->changeSales($v['userid'],$v['num'],$v['amount']);
            //更新商品销量
            $lama->changeStatic($v['goodsid'],$v['num']);
        }

        

        //确定收货后，同级推荐的永久奖励到账
        $rewardtemp=$this->dRewardtemp->selectOne("ordersn='".$order['ordersn']."'",'*');
        if(!empty($rewardtemp)){
            $this->dUser->update('id='.$rewardtemp['userid'],"amount=amount+{$rewardtemp['amount']}");

            $this->dRewardtemp->update('id='.$rewardtemp['id'],array('status' =>1));

        
            $user=$this->dUser->getById($rewardtemp['subid']);

            $top=$this->dUser->getById($rewardtemp['userid']);

            //记录
            $content2 = "你的下线".$user['nickname']."已经成功确定收货,您获得".$rewardtemp['amount']."元奖励";
            $data=array(
                   'userid' =>$top['id'],
                   'subid'  =>$user['id'],
                   'amount' =>$rewardtemp['amount'],
                   'content'=>$content2,
                   'addtime'=>time(),
                   'type'   =>4,
            );
            $this->dRecord->insert($data,true);  
            
            //模板消息
            $data_1 = array(
                'touser' => (string)$top['openid'],      
                'template_id' => $temp['order']['tid2'],     //模版id 
                'data' => array(
                    'first' => array('value' => '恭喜你，你的下级确定收货，你已受到奖励'),
                    'keyword1' => array('value' => date('Y-m-d H:i:s',$order['addtime'])),
                    'keyword2' => array('value' => $order['amount']),
                    'keyword3' => array('value' => $rewardtemp['amount']),
                    'remark' => array('value' => '奖励金额已到系统余额中'),
                )
            );
            $json_1 = json_encode($data_1);
            $da1 = $weixin->setNotice($json_1);
        }
    }
}
