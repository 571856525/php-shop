<?php
/**
 * 优惠券首页
 * User: DELL
 * Date: 2016/6/22
 * Time: 12:33
 */
class Action_Index extends App_Action{

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
	    if($this->getRequest()->isGet()){
            $this->setView(Blue_Action::VIEW_SMARTY2);//SMARTY2
            //优惠券列表 
            $coupons = $this->sCoupons->getAllList($sess['id']);
            if(!empty($coupons)) {
                foreach ($coupons as &$item) {
                    $item['create_time'] = date('Y-m-d',$item['create_time']);
                    $item['end_time'] = date('Y-m-d',$item['end_time']);
                    if($item['money']){
                        $item['money'] = $item['money']/10;
                    }
                }
            }
            return array('list'=>$coupons);
        }
	}
	public function __complete(){
	}
}
