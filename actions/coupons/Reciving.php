<?php
/**
 * 领取优惠券
 * User: GouHui
 * Date: 2018/11/30
 * Time: 12:33
 */
class Action_Reciving extends App_Action{

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
        $req['userid']=$sess['id'];
        $haves=$this->sCoupons->IfHaving($sess['id'],$req['logo']);
        if(!empty($haves)){
            //已经领取
            $state=3;
        }else{
            Blue_Commit::call('Coupons_Create',$req);
            $have=$this->sCoupons->IfHaving($sess['id'],$req['logo']);
            if(!empty($have)){
                //领取成功
                $state=1;
            }else{
                //领取失败
                $state=2;
            }
        }  
	    return array('state'=>$state);
	}
	public function __complete(){
	}
    public function verify(){
        $rule = array(
            'logo'     => array('filterStrlen', array(1, 20)),
        );
        return Blue_Filter::filterArray($_POST,$rule);
    }
}
