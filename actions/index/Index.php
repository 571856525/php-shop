<?php

/**
 * 首页
 *
 * @author xuefei@yunbix.com
 */

class Action_Index extends App_Action
{
    private $sGoods;
    private $sAd;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sGoods = new Service_Goods();
        $this->sGoodstype = new Service_Goodstype();
        $this->sAd = new Service_Ad();
        $this->sReward = new Service_Reward();
        $this->sUser= new Service_User();
        $this->sPromotion= new Service_Promotion();
        $this->sCoupons= new Service_Coupons();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {       
        $sess = $this->getSession();
        if ($this->getRequest()->isGet()) {
            $from=$_GET['from'];
            $classid =!empty($_GET['classid']) ? intval($_GET['classid'])  : 0;
            $ad = $this->sAd->getList(1);
            //获取分类 
            $type=$this->sGoodstype->getList(0); 
            foreach($type as &$item)
            {
                $item['list']=$this->sGoodstype->getList($item['id']); 
            }
            $data = $this->sGoods->getList($classid,1,999);
            foreach($data as $key=>$value){       


                //属于秒杀商品的
                $promotion2=$this->sPromotion->getOne(1,$value['id']);
                if(!empty($promotion2) && $promotion2['num']>1){
                    $data[$key]['pro_state'] =2;
                    $data[$key]['pro_amount']=$promotion2['amount'];
                    $data[$key]['start_time']=date('Y-m-d H:i:s',$promotion2['start_time']);
                    $data[$key]['end_time']=date('Y-m-d H:i:s',$promotion2['end_time']);
                    $data[$key]['number']=$promotion2['num'];
                    $data[$key]['sum']=$promotion2['sum'];
                    $data[$key]['image']=$promotion2['image'];
                    $spike_data[]=$data[$key];
                    unset($data[$key]);
                }
                //属于促销的满减
                $promotion3=$this->sPromotion->getOne(2,$value['id']);
                if(!empty($promotion3)){
                    $data[$key]['promotion']['pro_state'] =3;
                    $data[$key]['promotion']['start_time']=date('Y-m-d H:i:s',$promotion3['start_time']);
                    $data[$key]['promotion']['end_time']=date('Y-m-d H:i:s',$promotion3['end_time']);
                    $data[$key]['promotion']['num']=$promotion3['num'];
                    $data[$key]['promotion']['amount']=$promotion3['amount'];
                    $data[$key]['ac_title']=$promotion3['title'];
                    $give_data[]=$data[$key];
                    unset($data[$key]);
                }
                
                //属于促销的多买送
                $promotion4=$this->sPromotion->getOne(3,$value['id']);
                if(!empty($promotion4)){
                    $data[$key]['ac_title']=$promotion4['title'];
                    $data[$key]['promotion']['pro_state'] =4;
                    $data[$key]['promotion']['start_time']=date('Y-m-d H:i:s',$promotion4['start_time']);
                    $data[$key]['promotion']['end_time']=date('Y-m-d H:i:s',$promotion4['end_time']);
                    $data[$key]['promotion']['num']=$promotion4['num'];
                    $data[$key]['promotion']['send_type']=$promotion4['send_type'];
                    if($promotion4['send_type']==1){  //赠送商品
                        $data[$key]['promotion']['send_gid']=$promotion4['send_gid'];
                        $data[$key]['promotion']['send_num']=$promotion4['send_num'];
                    }else if($promotion4['send_type']==2){//赠送折扣卷
                        $data[$key]['promotion']['send_coupons_name']=$promotion4['send_coupons_name'];
                        $data[$key]['promotion']['send_amount']=$promotion4['send_amount'];  
                        $data[$key]['promotion']['send_full']=$promotion4['send_full']; 
                        $data[$key]['promotion']['send_end_time']=$promotion4['send_end_time']; 
                    }else if($promotion4['send_type']==3){//赠送满减卷
                        $data[$key]['promotion']['send_coupons_name']=$promotion4['send_coupons_name'];
                        $data[$key]['promotion']['send_reduce']=$promotion4['send_reduce'];  
                        $data[$key]['promotion']['send_full']=$promotion4['send_full']; 
                        $data[$key]['promotion']['send_end_time']=$promotion4['send_end_time'];
                    }
                    $give_data[]=$data[$key];
                    unset($data[$key]);
                }
            }

            // var_dump($spike_data);die;
            
            
            //排序：秒杀第一，促销第二，其他按商品的sort排序。
            // array_multisort($sort2,SORT_ASC,$sort1,SORT_ASC,$sort3,SORT_ASC,$data);    
            $user=$this->sUser->getById($sess['id']);

            //优惠券列表 ：
            $coupons = $this->sCoupons->getList(null);
            if(!empty($coupons)) {
                foreach ($coupons as $key=>$value) {
                    $coupons[$key]['create_time'] = date('Y-m-d',$coupons[$key]['create_time']);
                    $coupons[$key]['end_time'] = date('Y-m-d',$coupons[$key]['end_time']);
                    if($coupons[$key]['money']){
                        $coupons[$key]['money'] = $coupons[$key]['money']/10;
                    }
                    //自己是否已经领取优惠券
                    $have=$this->sCoupons->IfHaving($sess['id'],$coupons[$key]['logo']);
                    if(!empty($have)){
                        $coupons[$key]['is_have']=1;    
                        //是否使用？
                        // if($have['status']!=2){
                        //     unset($coupons[$key]);
                        //     continue;
                        // }
                        unset($coupons[$key]);
                        continue;
                    }
                    //该优惠券是否还有剩余
                    $exist=$this->sCoupons->IfExist($coupons[$key]['logo']);
                    if(empty($exist)){
                        $coupons[$key]['is_end']=1;
                    }
                }
            }
            $weixin = new App_Weixin();
            $sdk = $weixin->getSDK();

            /**
             *  spike_data   ：秒杀商品
             *  give_data    : 促销商品满减
             *  save_data    ：促销商品多买省活动
             *  coupons      ：优惠券
             */
            return array('list' => $data,'type' => $type,'ad' => $ad,'sdk' => $sdk , 'coupons'=>$coupons,'spike'=>$spike,'from'=>$from,'save_data'=>$save_data,'spike_data'=>$spike_data,'give_data'=>$give_data);
        }
        $this->setView(Blue_Action::VIEW_JSON);
        //优惠券列表 ：
        $coupons = $this->sCoupons->getList(null);
        if(!empty($coupons)) {
            foreach ($coupons as $key=>$value) {
                $coupons[$key]['create_time'] = date('Y-m-d',$coupons[$key]['create_time']);
                $coupons[$key]['end_time'] = date('Y-m-d',$coupons[$key]['end_time']);
                if($coupons[$key]['money']){
                    $coupons[$key]['money'] = $coupons[$key]['money']/10;
                }
                //自己是否已经领取优惠券
                $have=$this->sCoupons->IfHaving($sess['id'],$coupons[$key]['logo']);
                if(!empty($have)){
                    $coupons[$key]['is_have']=1;    
                    //是否使用？
                    // if($have['status']!=2){
                    //     unset($coupons[$key]);
                    //     continue;
                    // }
                    unset($coupons[$key]);
                    continue;
                }
                //该优惠券是否还有剩余
                $exist=$this->sCoupons->IfExist($coupons[$key]['logo']);
                if(empty($exist)){
                    $coupons[$key]['is_end']=1;
                }
            }
        }
        $coupons=array_values($coupons);
        return array('data'=>$coupons);
    }
}
