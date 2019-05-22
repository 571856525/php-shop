<?php

/**
 * 进出明细
 */

class Action_Detaile extends App_Action
{
    private $sTransfers;
    private $sDelivery;
    private $sUser;
    private $sCarts;
    private $sGoods;


    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->sTransfers = new Service_Transfers();
        $this->sUser = new Service_User();
        $this->sCarts = new Service_Carts();
        $this->sGoods = new Service_Goods();
        $this->sDelivery = new Service_Delivery();
        $this->sAddress= new Service_Address();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        if($this->getRequest()->isGet()){
            $rn =!empty($_GET['rn']) ? intval($_GET['rn']) : 8;
            $pn =!empty($_GET['pn']) ? intval($_GET['pn']) : 1;
            $lists=$this->getList($pn,$rn);
            $count=count($lists);
            return array('list'=>$lists,'page' => Blue_Page::pageInfo($count, $pn, $rn));
        }
        $this->setView(Blue_Action::VIEW_JSON);
        $ret=$this->verify();
        $pn=$ret['pn'];
        $rn=8;
        $lists=$this->getList($pn,$rn);
        $count=count($lists);
        return array('list'=>$lists,'page' => Blue_Page::pageInfo($count, $pn, $rn));
    }
    public function verify()
    {
        $rule = array(
            'pn' => array('filterIntBetweenWithEqual', array(0)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }
    public function getList($pn,$rn){
        $sess=$this->getSession(); 
        $list = $this->sTransfers->getListByUserId($sess['id']);  
        foreach ($list as &$datas){
            if($datas['fromid']==0){
                $datas['from']='云仓平台';
                $datas['type']=1;  //进库
            }
            if($datas['fromid']!=0 && $datas['fromid']!=$sess['id']){
                $user= $this->sUser->getById($datas['fromid']);
                $datas['from']=!empty($user['real_name'])?$user['real_name']:$user['nickname'];
                $datas['type']=1;  //进库
            }
            if($datas['fromid']!=0 && $datas['fromid']==$sess['id']){
                $user= $this->sUser->getById($datas['userid']);
                $datas['from']=!empty($user['real_name'])?$user['real_name']:$user['nickname'];
                $datas['type']=1;  //出库
            }
            $carts=$this->sCarts->getByOrderId($datas['id']);
            foreach($carts as &$v)
            {
                $info=$this->sGoods->getInfo($v['goodsid']);
                $v['goodsname']=$info['goodsname'];
                $v['goodspic']=$info['goodspic'];
            }
            $datas['goods']=$carts;
        }
        $delivery=$this->sDelivery->getListByUserid($sess['id']);
        foreach($delivery as &$value){
            //出库
            $value['type']=2;
            $address=$this->sAddress->getById($value['addressid']); 
            $value['from']=$address['contact'];
            $stock=json_decode($value['goods']);
            foreach($stock as &$v){
                $v=get_object_vars($v);
                $goods=$this->sGoods->getInfo($v['id']);            
                $v['goodsname']=$goods['goodsname'];
                $v['goodspic']=$goods['goodspic'];  
            }
            $value['goods']=$stock;
        }
        $lists=array_merge($list,$delivery);
        $sort=array();
        foreach($lists as &$value){
            $sort[]=$value['addtime'];
        }
        array_multisort($sort,SORT_DESC,$lists);
        $lists=array_slice($lists,($pn-1)*$rn,$rn);
        foreach($lists as &$value){
            $value['addtime']=date('Y-m-d H:i:s',$value['addtime']);
        }
        return $lists;
    }
}
