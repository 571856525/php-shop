<?php

/**
 * 资讯列表
 */

class Action_Index extends App_Action
{
    private $sArticle;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sArticle = new Service_Article();
        $this->sRecord = new Service_Record();
        $this->sUser = new Service_User();
        $this->sOrder = new Service_Order();
        $this->sTransfers = new Service_Transfers();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $sess=$this->getSession();
        if ($this->getRequest()->isGet()) {
            $cid =!empty($_GET['cid']) ? intval($_GET['cid']) : 1 ;
            $rn =!empty($_GET['rn']) ? intval($_GET['rn']) : 10;
            $pn =!empty($_GET['pn']) ? intval($_GET['pn']) : 1;
            if(!empty($_GET['type'])){ 
                //系统消息（支付，提货，邀请关联等等）
                $data = $this->sRecord->getAllList($sess['id'],$pn,$rn);
                $count=count( $data);
                foreach ($data as &$value) {
                    $value['addtime']=date('Y-m-d H:i:s',$value['addtime']);
                    if($value['subid']){
                        $user=$this->sUser->getById($value['subid']);
                        $value['nickname']=$user['nickname'];
                    }
                    if($value['type']==1){
                        $order=$this->sOrder->getByOrder($value['ordersn']);
                        $value['url']='/shop/order/info?id='.$order['id'];
                    }
                    if($value['type']==2){
                        $transfers=$this->sTransfers->getBySn($value['ordersn']);
                        $value['url']='/shop/transfers/info?id='.$transfers['id'];
                    }
                    if($value['type']==8){
                        $value['url']='/shop/coupons/index';
                    }
                }
            }else{
                 //通知通告
                 $data = $this->sArticle->getList($cid,$pn,$rn);
                 foreach ($data as &$value) {
                     $value['addtime']=date('Y-m-d H:i:s',$value['addtime']);
                 }
                $count=count( $data);
            }
            return array('list' => $data,'page' => Blue_Page::pageInfo($count, $pn, $rn));
        }
        //分页
        $this->setView(Blue_Action::VIEW_JSON);
        $ret=$this->verify();
        $rn=10;
        $pn=$ret['pn'];
        if(!empty($ret['type'])){ 
            //系统消息（支付，提货，邀请关联等等）
            $data = $this->sRecord->getAllList($sess['id'], $pn, $rn);
            $count=count( $data);
            foreach ($data as &$value) {
                $value['addtime']=date('Y-m-d H:i:s',$value['addtime']);
                if($value['subid']){
                      $user=$this->sUser->getById($value['subid']);
                      $value['nickname']=$user['nickname'];
                }
                if($value['type']==1){
                    $order=$this->sOrder->getByOrder($value['ordersn']);
                    $value['url']='/shop/order/info?id='.$order['id'];
                }
                if($value['type']==2){
                    $transfers=$this->sTransfers->getBySn($value['ordersn']);
                    $value['url']='/shop/transfers/info?id='.$transfers['id'];
                }
            }
        }else{
            //通知通告
            $data = $this->sArticle->getList(1,$pn,$rn);
            foreach ($data as &$value) {
                $value['addtime']=date('Y-m-d H:i:s',$value['addtime']);
            }
            $count=count( $data);
        }
        return array('list' => $data,'page' => Blue_Page::pageInfo($count, $pn, $rn));
        
    }
    public function verify()
    {
        $rule = array(
            'pn' => array('filterIntBetweenWithEqual', array(0)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        $req['type']=$_POST['type'];
        return $req;
    }

}
