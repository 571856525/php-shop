<?php

/**
 * 商品信息
 */

class Action_Info extends App_Action
{
    private $sGoods;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        //$this->setView(Blue_Action::VIEW_JSON);
        $this->sGoods = new Service_Goods();
        $this->sComments = new Service_Comments();
        $this->sUser= new Service_User();
        $this->sReward= new Service_Reward();
        $this->sAd= new Service_Ad();
        $this->sCollection= new Service_Collection();
        $this->sGoodstype = new Service_Goodstype();
        $this->sCoupons = new Service_Coupons();
        $this->sPromotion= new Service_Promotion();
        $this->sCombination= new Service_Combination();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $sess=$this->getSession();
        $id =!empty($_GET['id']) ? intval($_GET['id']) : 0;
        $pn = !empty($_GET['pn']) ?  intval($_GET['pn']) : 1;
        $rn = !empty($_GET['rn']) ?  intval($_GET['rn']) : 10;
        if(empty($id))
        {
            $this->Warning('ID不能为空');
        }
        $data = $this->sGoods->getById($id);
        if($data['status']==0){
            $this->Warning('该商品已下架！！');
        }
        $data['photo']=unserialize($data['photo']);

        //评论
        $list=$this->sComments->getList($id,$pn,$rn);
        //评论数
        $count=$this->sComments->getCount($id);
        foreach ($list as &$value) {
            $value['create_time']=date('Y-m-d H:i:s',$value['create_time']);
            $user=$this->sUser->getById($value['userid']);
            $value['nickname']=$user['nickname'];
            $value['headimgurl']=$user['headimgurl'];
        }
        //是否收藏
        $collection=$this->sCollection->getOne($sess['id'],$id);
        $collection=!empty($collection)?1:0;
        
        $weixin=new App_Weixin();
        $sdk=$weixin->getSDK();
        $user=$this->sUser->getById($sess['id']);

        //优惠券列表 ：
        $coupons = $this->sCoupons->getList($id);
        if(!empty($coupons)) {
            foreach ($coupons as &$item) {
                $item['create_time'] = date('Y-m-d',$item['create_time']);
                $item['end_time'] = date('Y-m-d',$item['end_time']);
                if($item['money']){
                    $item['money'] = $item['money']/10;
                }
                $have=$this->sCoupons->IfHaving($sess['id'],$item['logo']);
                //是否领取
                if(!empty($have)){
                    $item['is_have']=1;
                }
                if($item['goods_id']){
                    $item['goodsname']=  $data['goodsname'];
                }
                //是否领完
                $exist=$this->sCoupons->IfExist($item['logo']);
                if(empty($exist)){
                   $item['is_end']=1;
                }
            }
        }


        //组合套餐列表
        $comList=$this->sCombination->getList($id);
        if($comList){
            foreach ($comList as $key => $value) {
                if($value['goods_id1']){
                    $comList[$key]['goods1']=$this->sGoods->getInfo($value['goods_id1']);
                }
                if($value['goods_id2']){
                    $comList[$key]['goods2']=$this->sGoods->getInfo($value['goods_id2']);
                }
                if($value['goods_id3']){
                    $comList[$key]['goods3']=$this->sGoods->getInfo($value['goods_id3']);
                }
                if($value['goods_id4']){
                    $comList[$key]['goods4']=$this->sGoods->getInfo($value['goods_id4']);
                }
                if($value['goods_id5']){
                    $comList[$key]['goods5']=$this->sGoods->getInfo($value['goods_id5']);
                }
            }
        }

        //是否含有优惠券
        $have=$this->sCoupons->getByPay($sess['id']);
        if(!empty($have)){
            $is_have=1;
        }
    
        $promotion=$this->sPromotion->getIfOne($id);
        if(!empty($promotion)){
            $data['ac_title']= $promotion['title'];
            $data['start_time']=date('Y-m-d H:i:s',$promotion['start_time']);
            $data['end_time']=date('Y-m-d H:i:s',$promotion['end_time']);
            //秒杀
            if($promotion['type']==1){
                if($promotion['num']>0){
                    $data['pro_state'] =2;
                    $data['pro_amount']=$promotion['amount'];
                    $data['number']=$promotion['num'];
                    $data['sum']=$promotion['sum'];
                }else{
                    unset($promotion);
                }
            }
            //多买省
            if($promotion['type']==2){
                $data['promotion']['pro_state'] =3;
                $data['promotion']['start_time']=date('Y-m-d H:i:s',$promotion3['start_time']);
                $data['promotion']['end_time']=date('Y-m-d H:i:s',$promotion3['end_time']);
                $data['promotion']['num']=$promotion3['num'];
                $data['promotion']['amount']=$promotion3['amount'];
               
            }
            //多买送
            if($promotion['type']==3){
                $data['promotion']['pro_state'] =4;
                $data['promotion']['start_time']=date('Y-m-d H:i:s',$promotion4['start_time']);
                $data['promotion']['end_time']=date('Y-m-d H:i:s',$promotion4['end_time']);
                $data['promotion']['num']=$promotion4['num'];
                $data['promotion']['send_type']=$promotion4['send_type'];
                if($promotion4['send_type']==1){  //赠送商品
                    $data['promotion']['send_gid']=$promotion4['send_gid'];
                    $data['promotion']['send_num']=$promotion4['send_num'];
                }else if($promotion4['send_type']==2){//赠送折扣卷
                    $data['promotion']['send_coupons_name']=$promotion4['send_coupons_name'];
                    $data['promotion']['send_amount']=$promotion4['send_amount'];  
                    $data['promotion']['send_full']=$promotion4['send_full']; 
                    $data['promotion']['send_end_time']=$promotion4['send_end_time']; 
                }else if($promotion4['send_type']==3){//赠送满减卷
                    $data['promotion']['send_coupons_name']=$promotion4['send_coupons_name'];
                    $data['promotion']['send_reduce']=$promotion4['send_reduce'];  
                    $data['promotion']['send_full']=$promotion4['send_full']; 
                    $data['promotion']['send_end_time']=$promotion4['send_end_time'];
                }
            }
        }
        $adList = $this->sAd->getList(3);
        return array('comList'=>$comList, 'data' => $data,'cs'=> $adList[0]['adpic'],'list'=>$list,'sizelist'=>$sizelist,'count'=>$count,'sdk'=>$sdk,'collection'=>$collection,'coupons'=>$coupons,'is_have'=>$is_have);
    }
}
