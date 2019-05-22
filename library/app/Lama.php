<?php
/**
 * 辣妈与小宝通用
 */
class App_Lama
{
    private $dUser;
	private $dWarehouse;
    private $dSales;
    private $dRate;
    private $dLog;
    private $dInvite;
    private $dReward;

	public function __construct(){
		$this->dUser = new Dao_User();
		$this->dWarehouse = new Dao_Warehouse();
        $this->dSales = new Dao_Sales();
        $this->dRate = new Dao_Rate();
        $this->dLog = new Dao_Log();
        $this->dInvite = new Dao_Invite();
        $this->dReward = new Dao_Reward();
        $this->dRecord = new Dao_Record();
        $this->dStatic = new Dao_Static();
        $this->dOrder = new Dao_Order();
        $this->dGoods = new Dao_Goods();
        $this->dGoodstype = new Dao_Goodstype();
        $this->dCart = new Dao_Cart();
        $this->dTransfers = new Dao_Transfers();
        $this->dCarts = new Dao_Carts();
        $this->dReward = new Dao_Reward();
        $this->dRewardtemp = new Dao_Rewardtemp();

	}
	//入库
    public function changeWare($userid, $goodsid,$num,$money,$type=0)
    {
        //查询库存
        $ware =$this->dWarehouse->getBygoodsId($userid,$goodsid);
        core_log::debug('更新库存----***用户:'.$userid.'***商品ID:'.$goodsid.'***数量:'.$num.'***金额'.$money);
        if(!empty($ware))
        {
            $data=array(
                'userid'=>$userid,
                'goodsid'=>$goodsid,
                'on_inventory'=>$ware['on_inventory']+$num,
            );
            $this->dWarehouse->update(sprintf('id=%d', $ware['id']), $data);
        }
        else
        {
            $data=array(
                'userid'=>$userid,
                'goodsid'=>$goodsid,
                'on_inventory'=>$num,
            );
            $this->dWarehouse->insert($data, true);   
        } 
        //云仓的话直接更新月度销量,零售的话就得确定收货后再更新月度销量
        if($type){
            $this->changeSales($userid,$num,$money);
        }
        
    }

    //递归更新月度总销量
    public function changeSales($userid,$num,$money,$level=0)
    {   core_log::debug('*************************************');
        //更新月度销量增加
        $month=intval(date('m',time()));
        $year=date('Y',time());
        //获取月度销量
        $sale =$this->dSales->getByIdMonth($userid, $month,$year);
        core_log::debug('更新月度销量----***用户:'.$userid.'***商品ID:'.$goodsid.'***数量:'.$num);
        $my_num=  $level<=0 ? $num : 0; 
        $my_money=  $level<=0 ? $money : 0; 
        if(!empty($sale))
        {
            $data=array(
                'num'=>$sale['num']+$num,
                'my_num'=>$sale['my_num']+$my_num,
                'money'=>$sale['money']+$money,
                'my_money'=>$sale['my_money']+$my_money,
                'month'=>$month,
                'year'=>$year
            );    
            $this->dSales->update(sprintf('id=%d', $sale['id']), $data);
        }
        else
        {
            $data=array(
                'userid'=>$userid,
                'month'=>$month,
                'year'=>$year,
                'num'=>$num,
                'my_num'=>$my_num,
                'money'=>$money,
                'my_money'=>$my_money
            );
            $this->dSales->insert($data, true);   
        }
        //查询是否有上级
        $invite=$this->dInvite->getById($userid);
        if(!empty($invite))
        {
            core_log::debug('更新上级月度销量----***用户:'.$invite['rid'].'***商品ID:'.$goodsid.'***数量:'.$num);
            $this->changeSales($invite['rid'],$num,$money,$level+1);
        }
    }
    //计算月度奖励金
    public function getMonthBonus($code) { 
        $rate=$this->dRate->getcode($code);
        return $code*$rate['code'];
    }

    /*
    //提成奖励
    //code:金额    uid:用户id    $ordersn:订单号
    */
    // public function getreward($num,$code,$uid) {
    //     //获取上级用户 
    //     $invite=$this->dInvite->get($uid);
    //     //用户等级信息
    //     $user= $this->dUser->getById($uid); 

    //     if(!empty($invite))
    //     {
    //         //1级用户提成比例计算
    //         $top_user= $this->dUser->getById($invite['rid']); 
            
    //         //获取该等级用户的佣金比例
    //         $reward= $this->dReward->get($top_user['reward']); 
    //         //计算提成
    //         if(!empty($reward['user'.$user['reward']]))
    //         {
    //             //奖励金额
    //             $jl=$code*$reward['user'.$user['reward']]/100;
    //             //收支记录
    //             $data=array(
    //                 'userid'=>$top_user['id'],
    //                 'subid' =>$user['id'],
    //                 'ordersn'=>$ordersn,
    //                 'amount'=>$jl,
    //                 'type'=>4,
    //                 'content'=>'下级用户'.$user['nickname'].'（'.$user['id'].'）花费'.$code.'购买了商品,提成奖励：'.$jl,
    //                 'addtime' => time(),
    //                 'close'  => 1
    //             );
    //             $this->dRecord->insert($data, true);

    //             //确定收货后进行到账
    //             $this->dUser->update(sprintf('id=%d', $top_user['id']), array('jl_amount'=>$top_user['jl_amount']+$jl));
    //         }

    //             //二级用户提成比例计算
    //             //获取上级用户 
    //             $invite=$this->dInvite->get($top_user['id']);
    //             if(!empty($invite))
    //             {
    //                 //等级3以上提成奖励
    //                 $tops_user= $this->dUser->getById($invite['rid']); 
    //                 if($tops_user['reward']>=3)
    //                 {
    //                     //获取该等级用户的佣金比例
    //                     $reward= $this->dReward->get($tops_user['reward']); 
    //                     if(!empty($reward['user'.$top_user['reward']]))
    //                     {
    //                         //奖励金额
    //                         $jl=$code*$reward['user'.$top_user['reward']]/100;
    //                         //收支记录
    //                         $data=array(
    //                             'userid'=>$tops_user['id'],
    //                             'subid' =>$user['id'],
    //                             'ordersn'=>$ordersn,
    //                             'amount'=>$jl,
    //                             'type'=>4,
    //                             'content'=>'下2级用户'.$user['nickname'].'（'.$user['id'].'）花费'.$code.'购买了商品,提成奖励：'.$jl,
    //                             'addtime' => time(),
    //                             'close'=>1
    //                         );
    //                         $this->dRecord->insert($data, true);

    //                         //确定收货后进行到账
    //                         $this->dUser->update(sprintf('id=%d', $tops_user['id']), array('jl_amount'=>$tops_user['jl_amount']+$jl));
    //                     }
    //                 }
                    
                    
    //             }    
    //     }
    //     //获取上级用户 
    //     $invite=$this->dInvite->get($uid);
    //     //用户等级信息
    //     $user= $this->dUser->getById($uid); 
    //     if(!empty($invite))
    //     {
    //         //1级用户提成比例计算
    //         $top_user= $this->dUser->getById($invite['rid']); 
    //         //判断上级用户等级是否大于等于本人
    //         if($top_user['reward']>=$user['reward'])
    //         {
    //             //获取该等级用户的佣金比例
    //             $reward= $this->dReward->get($top_user['reward']); 
    //             //计算提成
    //             if(!empty($reward['re_one'.$user['reward']]))
    //             {
    //                 //奖励金额
    //                 $jl=$num*$reward['re_one'.$user['reward']];
    //                 $jl=number_format($jl,2);
    //                 //收支记录
    //                 $data=array(
    //                     'userid'=>$top_user['id'],
    //                     'subid' =>$user['id'],
    //                     'ordersn'=>$ordersn,
    //                     'amount'=>$jl,
    //                     'type'=>4,
    //                     'content'=>'下级用户'.$user['nickname'].'（'.$user['id'].'）花费'.$code.'购买了商品,提成奖励：'.$jl,
    //                     'addtime' => time(),
    //                     'close'  => 1
    //                 );
    //                 $this->dRecord->insert($data, true);

    //                 //确定收货后进行到账
    //                 $this->dUser->update(sprintf('id=%d', $top_user['id']), array('jl_amount'=>$top_user['jl_amount']+$jl));
    //             }
    //         }
    //     }
    // }

    /*
    //写入操作日志
    //$data ：插入数据
    */
    public function insertLog($data) { 
        $this->dLog->insert($data,true);

    }

    /*
    //关联用户
    //uid:用户ID  rid:推荐人ID
    */
    public function changeInvite($uid,$rid) {
        if(!empty($uid) && !empty($rid))
        {
            $invite=$this->dInvite->getById($uid);//查询关联关系
            $top_user= $this->dUser->getById($rid); //查询上级用户信息
            $user=$this->dUser->getById($uid);//用户的具体信息
            $rewardUser=$this->dUser->getById($rid);//推荐人的具体信息
            if(empty($invite))
            {
                $data=array(
                    'uid'  =>$uid,
                    'create_time'  =>time(),
                    'rid'  =>$rid,
                    'end'  =>time()+3600*24*90
                );
                $this->dInvite->insert($data,true);
            }
            else
            {
                if($invite['end']<=0)
                {
                    $data=array(
                        'uid'  =>$uid,
                        'create_time'  =>time(),
                        'rid'  =>$rid,
                        'end'  =>time()+3600*24*90
                    );
                    $this->dInvite->update('id='.$invite['id'],$data) ;
                }
            }
            //判断上级是否可以升级VIP
            
            // if($top_user['reward']<2)
            // {   
            //     $this->dUser->update('id='.$top_user['id'],array('reward'=>2));   
            // }

            //关联记录
            $data=array(
                'userid'=>$rid,
                'subid' =>$uid,
                'amount'=>0,
                'ordersn'=>'',
                'type'=>5,
                'content'=>'恭喜您,邀请用户：'. $user['nickname'].'关联完成',//.',奖励金额：'.$order['ordersn'],
                'addtime' => time()
            );
            $this->dRecord->insert($data, true);



            $weixin=new App_Weixin();
            // $temp = Arch_Yaml::get('template');
            // $data = array(
            //     'touser' => (string)$top_user['openid'],     //openid
            //     'template_id' => $temp['user']['tid1'],     //模版id 
            //     'data' => array(
            //         'first' => array('value' => '🔔🔔🔔恭喜您,你的下线和你关联！！！'),
            //         'keyword1' => array('value' => $user['nickname']),
            //         'keyword2' => array('value' => date('Y-m-d H:i:s',time())),
            //         'remark' => array('value' => '感谢您的支持'),
            //     )
            // );
            // $json = json_encode($data);
            // $weixin->setNotice($json);


            $url = $temp['then']['url'];
            //调用客服推送
            $content = '	成功关联通知！！！   ' . "\n" .
                "\n" .
                //			'------------------------------'."\n".
                '哇！您太有号召力了!一位新伙伴与您关联!' . "\n" .
                "\n" .
                //			'------------------------------'."\n".
                '姓  名 : ' . $user['nickname'] . "\n" .
                "\n" .
                //			'------------------------------'."\n".
                '时	 间 : ' . date('Y-m-d H:i:s', TIMESTAMP) . "\n" .
                "\n";
            $weixin->pushMsg($top_user['openid'], 'text', $content);
        }    
    } 
    //更新每个商品的销量 
    public function changeStatic($gid,$num){
        $statis=$this->dStatic->get($gid);
        if(empty($statis)){
             $data=array(
                'gid'=>$gid,
                'num'=>$num,
             );
             $this->dStatic->insert($data,true); 
       }else{
            $this->dStatic->update('gid='.$gid,"num=num+{$num}"); 
       }
    }

    //获取订单商品总数 
    public function getOrderCount($ordersn){
        $num= 0;
        $order=$this->dOrder->getByOrder($ordersn);
        Core_log::debug('订单数量111---------->'.json_encode($order));
        if(!empty($order)){
           $num=$this->dCart->getOrderCount($order['id']);
        }
        return $num;
    }

    //获取云仓订单商品总数 
    public function getTransfersCount($ordersn){
        $num= 0;
        $order=$this->dTransfers->getBySn($ordersn);
        if(empty($order)){
           $num=$this->dCarts->getTransfersCount($order['id']);
        }
        return $num;
    }
    

    //永久奖励
    public function changeReward($userid,$rid,$num,$ordersn){
        $user=$this->dUser->getById($userid); 
        $reward=$this->dReward->get($user['reward']);
        //奖励钱数
        $money=$num*$reward['re_one'.$user['reward']];
        $data=array(
           'userid'       => $rid,
           'subid'        => $userid,
           'ordersn'      => $ordersn,
           'amount'       => $money,
           'create_time'  => time()
        );
        $this->dRewardtemp->insert($data,true);


    }
}
