<?php
/**
 * 优惠券列表
 * User: DELL
 * Date: 2016/6/22
 * Time: 12:33
 */
class Action_GetList extends App_Action{

	private $sCoupons;
	public function __prepare(){
        //授权回调
        $this->hookNeedMsg = true;
		$this->setView(Blue_Action::VIEW_JSON);
        $this->sCoupons = new Service_Coupons();
        $this->sGoods = new Service_Goods();
	}
	public function __execute(){
        $sess = $this->getSession();
        $req = $this->verify();
   
        $goods=$this->sGoods->getById($req['id']);
        $amount+=$goods['amount']*$req['num'];

	    $coupons=$this->sCoupons->getList($sess['openid']);
        foreach($coupons as $key=>$value){
            // if(!empty($value['goods_id'])){
            //     if($value['goods_id']!=$id){
            //         unset($coupons[$key]);
            //     }
            // }
            // if(!empty($value['goods_type'])){
            //     if($value['goods_type']!=$goods['classid']){
            //         unset($coupons[$key]);
            //     }
            // }
            if($coupons['full']>=$amount){
                unset($coupons[$key]);
                continue;   
            }
            if($value['type']==0){
                $sort[$key]=$coupons[$key]['price']=$amount*$value['money']/100;
            }
            if($value['type']==1 && $coupons['full']<=$amount){
                $sort[$key]=$coupons[$key]['price']=$amount - $coupons['reduce'];
            }
        }
        //默认最优惠方案
        array_multisort($sort,SORT_DESC,$coupons);
	    return array('coupons'=>$coupons);
	}
	public function __complete(){

	}
    public function verify(){
        $rule = array(
            'id' => array('filterIntBetweenWithEqual', array(0)),
            'num' => array('filterIntBetweenWithEqual', array(0)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
    }
}
