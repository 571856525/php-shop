<?php

/**
 * 提货订单
 */

class Action_Purchase extends App_Action
{
    private $sTransfers;
    private $sUser;


    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->NeedLogin = true;
        $this->sTransfers = new Service_Transfers();
        $this->sUser = new Service_User();
        $this->sCarts = new Service_Carts();
        $this->sGoods = new Service_Goods();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }
    public function __execute()
    {
        $sess=$this->getSession();
        if($this->getRequest()->isGet()){
            $rn =!empty($_GET['rn']) ? intval($_GET['rn']) : 6;
            $pn =!empty($_GET['pn']) ? intval($_GET['pn']) : 1;
            $type =$_GET['type'];
            $audit=$_GET['audit'];
            if(empty($type)){
                //平台进货订单
                $list = $this->sTransfers->getListByPlatform($sess['id'],$pn,$rn); 
                // $this->log($list); 
            }else{
                /**
                 * $type  1 ：我向上级进货    2 ：下级向我进货
                 * $audit 0 ：待审核          1 ：已审核
                 */
                $list = $this->sTransfers->getAllListById($sess['id'],$type,$pn,$rn,$audit);  
            }
            if($list){
                foreach ($list as &$datas){
                // if($datas['fromid']){
                //    $datas['from'] = $this->sUser->getById($list['fromid']);
                // }
                if($datas['fromid']!=0){
                    if($datas['state']!=2){
                        if($datas['state']==1){
                            if($sess['id']==$datas['userid']){
                                if($datas['audit']==0){
                                    //待审核  
                                    $datas['state']=3;   
                                }
                            }
                            if($sess['id']==$datas['fromid']){
                                if($datas['audit']==0){
                                    //审核 
                                    $datas['state']=4;   
                                }
                            }     
                        }
                    }
                }

                //出现取消订单按钮
                if($type==1){
                    if($datas['state']!=2 && $datas['state']!=1){
                        $datas['type']=1;
                    }
                }
                $carts=$this->sCarts->getByOrderId($datas['id']);
                $sum=0;
                foreach($carts as &$v)
                {
                    $v['goods']=$this->sGoods->getInfo($v['goodsid']);
                    $sum+=$v['num'];
                }
                $datas['carts']=$carts;
                $datas['sum']=$sum;
                $datas['addtime'] = date("Y-m-d H:i:s",$datas['addtime']);
                }
            }
            $count=count($list);
            return array('list' => $list,'sess'=>$sess,'page' => Blue_Page::pageInfo($count, $pn, $rn));
        } 
        $this->setView(Blue_Action::VIEW_JSON);
        $req=$this->verify();
        $rn=6;
        $pn=$req['pn']; 
        $type=$req['type'];
        $audit=$req['audit'];
        if(empty($type)){
            //平台进货订单
            $list = $this->sTransfers->getListByPlatform($sess['id'],$pn,$rn); 
            // $this->log($list); 
        }else{
            /**
             * $type  1 ：我向上级进货    2 ：下级向我进货
             * $audit 0 ：待审核          1 ：已审核
             */
            core_log::debug($audit);
            $list = $this->sTransfers->getAllListById($sess['id'],$type,$pn,$rn,$audit);  
        }
        if($list){
            foreach ($list as &$datas){
            // if($datas['fromid']){
            //    $datas['from'] = $this->sUser->getById($list['fromid']);
            // }
            if($datas['fromid']!=0){
                if($datas['state']!=2){
                    if($datas['state']==1){
                        if($sess['id']==$datas['userid']){
                            if($datas['audit']==0){
                                //待审核  
                                $datas['state']=3;   
                            }
                        }
                        if($sess['id']==$datas['fromid']){
                            if($datas['audit']==0){
                                //审核 
                                $datas['state']=4;   
                            }
                        }     
                    }
                }
            }
            //我向上级
            if($type==1){
                if($datas['state']!=2 && $datas['state']!=1){
                    $datas['type']=1;
                }
            }
            $carts=$this->sCarts->getByOrderId($datas['id']);
            $sum=0;
            foreach($carts as &$v)
            {
                $v['goods']=$this->sGoods->getInfo($v['goodsid']);
                $sum+=$v['num'];
            }
            $datas['carts']=$carts;
            $datas['sum']=$sum;
            $datas['addtime'] = date("Y-m-d H:i:s",$datas['addtime']);
            }
        }
        $count=count($list);
        return array('list' => $list);     
    }
    public function verify()
    {
        $rule = array(
            'pn' => array('filterIntBetweenWithEqual', array(0)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        $req['audit']=$_POST['audit'];
        $req['type']=$_POST['type'];
        return $req;
    }
}
