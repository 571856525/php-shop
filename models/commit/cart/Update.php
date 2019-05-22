<?php

/**
 * 购物车更新
 */

class Commit_Cart_Update extends Blue_Commit
{
    private $dCart;
    private $dGoods;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dCart = new Dao_Cart();
        $this->dGoods = new Dao_Goods();
        $this->dPromotion = new Dao_Promotion();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        
        if($req['goods'])
        {
            $goods=json_decode($req['goods'],true);
            foreach($goods['id'] as $k=>$v)
            {  
                $cart= $this->dCart->getId($v);
                //获取商品信息
                $sg=$this->dGoods->getById($cart['goodsid']);

                // if($goods['pid'][$k]){
                //     $promotion=$this->dPromotion->get($goods['pid'][$k]);
                // }
                // if(!empty($promotion)){
                //     if($promotion['type']==1 && $promotion['num']>0){
                //         $sg['amount']=$promotion['amount'];
                //     }
                //     if($promotion['type']==2){
                //         $give=floor($goods['num'][$k]/$promotion['num'])*$promotion['give'];
                //     }
                //     if($promotion['type']==3){
                //         $amount=$sg['amount']* $goods['num'][$k]; 
                //         if($amount>$promotion['amount']){
                //             $amount-=$promotion['reduce'];
                //         }
                //         //标志价格改变了
                //         $type=1;
                //     }
                // }
                $amount=$sg['amount']* $goods['num'][$k]; 
                $data=array(
                    'amount'=>$amount,
                    'num'=>$goods['num'][$k],
                );
                $this->dCart->update(sprintf('id=%d', $cart['id']), $data);
            }
        }
        
    }
}
