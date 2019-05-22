<?php

/**
 * 调拨审核
 */

class Commit_Transfers_Audit extends Blue_Commit
{
    private $dTransfers;
    private $dCarts;
    private $dWarehouse;
    private $dRecord;
    private $dUser;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dTransfers = new Dao_Transfers();
        $this->dCarts = new Dao_Carts();
        $this->dWarehouse = new Dao_Warehouse();
        $this->dRecord = new Dao_Record();
        $this->dUser = new Dao_User();
        $this->lama = new App_Lama();
        $this->amount_log = new Dao_Amountlog();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        //审核
        Core_log::Warning('审批---------->'.json_encode($req));
        $this->dTransfers->update('id='.$req['id'], array('audit'=>$req['audit']));
        $transfers=$this->dTransfers->getById($req['id']);
        $user=$this->dUser->getById($transfers['fromid']);
        $user2=$this->dUser->getById($transfers['userid']);
        if($req['audit'] == 1)
        {
            $carts =$this->dCarts->getByOrderId($req['id']);
            //判断是否从上级调货
            if(!empty($transfers['fromid']))
            {
                foreach($carts as $v)
                {
                    $warehouse= $this->dWarehouse->getBygoodsId($transfers['fromid'],$v['goodsid']);
                    if($warehouse['on_inventory']>=$v['num'])
                    {
                        //用户钱数到上级用户的账号里面
                        if($transfers['price']){
                            $transfers['amount']=$transfers['price'];
                        }
                        $amount=$user['amount']+$transfers['amount'];
                        $this->dUser->update('id='.$transfers['fromid'],array('amount'=>$amount));
                        $this->amount_log->insert(array(
                            'amount'     =>$transfers['amount'],
                            'create_time'=>time(),
                            'tid'        =>$req['id']
                        ),true);



                        $data=array(
                            'userid'=>$transfers['fromid'],
                            'amount'=>$transfers['amount'],
                            'ordersn'=>$transfers['ordersn'],
                            'type'=>8,
                            'addtime' => time()
                        );
                        $data['content']='您成功审核下级订单，你的余额增加'.$transfers['amount'];
                        $this->dRecord->insert($data, true);
                        

                        // $this->dWarehouse->update('id='.$warehouse['id'], array('on_inventory'=>$warehouse['on_inventory']-$v['num']));
                        //下级用户增加库存
                        $this->lama->changeWare($transfers['userid'],$v['goodsid'],$v['num'],$v['amount'],1);
                    }
                }
            }  
            
            
            Core_log::debug('审批--222222-------->'.json_encode($req));
            $weixin=new App_Weixin();
            $temp = Arch_Yaml::get('template');
            core_log::debug($user['openid']);

            $data = array(
                'touser' => (string)$user2['openid'],     //openid
                'template_id' => $temp['order']['tid4'],     //模版id 
                'topcolor' => '#FF0000',
                'data' => array(
                    'first' => array('value' => '你的云仓订单已经审核通过'),
                    'keyword1' => array('value' => $transfers['ordersn']),
                    'keyword2' => array('value' => date('Y-m-d H:i:s',time())),
                    'keyword3' => array('value' => '审核通过'),
                    'remark' => array('value' => '感谢您对本商城的信任，欢迎再次购买呦！！！'),
                )
            );
            $json = json_encode($data);
            $weixin->setNotice($json);

            //通知上级
            if(!empty($transfers['fromid'])){
                $first ='你成功审核了'.$user2['nickname'].'的订单';
                $url="http://".$_SERVER['HTTP_HOST']."/shop/user/wallet";
                $remark= "你收到".$transfers['amount']."货款，请进入我的余额进行查询";
                $data = array(
                    'touser' => (string)$user['openid'],     //openid
                    'template_id' => $temp['order']['tid4'],     //模版id 
                    'topcolor' => '#FF0000',
                    'data' => array(
                        'first' => array('value' =>  $first),
                        'keyword1' => array('value' => $transfers['ordersn']),
                        'keyword2' => array('value' => date('Y-m-d H:i:s',time())),
                        'keyword3' => array('value' => '审核通过'),
                        'remark' => array('value' =>$remark),
                    )
                );
                $json = json_encode($data);
                $weixin->setNotice($json);
            }
            Core_log::debug('审批--222222------33333-------444444------>');
            
        }
        elseif($req['audit'] == 2)
        {
        
            //审批不通过欠款退到余额
            $transfers = $this->dTransfers->getById($req['id']); 
            //查询余额
            $user= $this->dUser->getById($transfers['userid']); 
            //退款
            $this->dUser->update('id='.$transfers['userid'], array('amount'=>$user['amount']+$transfers['amount']));
            //记录
            $data=array(
                'userid'=>$transfers['userid'],
                'amount'=>$transfers['amount'],
                'ordersn'=>$transfers['ordersn'],
                'type'=>3,
                'content'=>'你的调拨审批不通过，退款到余额：'.$transfers['amount'],
                'addtime' => time()
            );
            $this->dRecord->insert($data, true);
        }	

    }
}
