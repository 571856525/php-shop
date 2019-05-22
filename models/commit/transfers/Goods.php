<?php

/**
 * 调拨申请商品信息
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_Transfers_Goods extends Blue_Commit
{
    private $dTransfers;
    private $dCarts;
    private $dGoods;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dTransfers = new Dao_Transfers();
        $this->dCarts = new Dao_Carts();
        $this->dGoods = new Dao_Goods();
        $this->dUser = new Dao_User();
        $this->dReward = new Dao_Reward();
        $this->dPromotion = new Dao_Promotion();
        $this->dCoupons   = new Dao_Coupons();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        //根据订单号调拨信息
        $transfers = $this->dTransfers->getBySn($req['ordersn']);
        core_log::debug('---diaoiunasc'.json_encode($req['id']));
        if($req['id'])
        {
            foreach($req['id'] as $k=>$v)
            {  
                //获取商品信息
                $sg=$this->dGoods->getById($v);
                $price=$sg['amount'];
                $user=$this->dUser->getById($transfers['userid']);
                if($user['reward']>1){
                    $reward=$this->dReward->get($user['reward']); 
                    $price=$reward['re_price'];
                }
                // if($req['num'][$k]){
                //     $promotion=$this->dPromotion->get($req['pro_id'][$k]);
                //     if($promotion['type']==1 && $promotion['amount']<$price){
                //         $price=$promotion['amount'];
                //     }
                //     //"买省活动"是否在云仓显示 有待商榷

                //     //买赠活动
                //     if($promotion['type']==3 && $promotion['num']<=$req['num'][$k]){
                //           if($promotion['send_type']==1){
                //             //赠送商品
                //             $times=floor($req['num'][$k]/$promotion['num'])*$promotion['send_num'];
                //             $data=array(
                //                 'orderid'=>$transfers['id'],
                //                 'userid'=>$transfers['userid'],
                //                 'goodsid'=>$promotion['send_gid'],
                //                 'pro_id' =>$promotion['id'],
                //                 'num'    =>$times,
                //                 'is_send'=>1,
                //                 'addtime'=>$req['addtime']
                //             );
                //            $this->dCarts->insert( $data, true); 
                //           }else{
                //              //优惠卷赠送在支付成功后
                //           }
                //     }
                // }
                if($req['num'][$k]>0){
                    $data=array(
                        'orderid'=>$transfers['id'],
                        'userid'=>$transfers['userid'],
                        'goodsid'=>$v,
                        'pro_id' =>$req['pro_id'][$k],
                        'amount'=>  $price*$req['num'][$k],
                        'num'=>$req['num'][$k],
                        'addtime'=>$req['addtime']
                    );
                   $this->dCarts->insert( $data, true); 
                }
            }                
        }
    }
}
