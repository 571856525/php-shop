<?php

/**
 * 订单详情
 */

class Action_Info extends App_Action
{

    private $sOrder;
    private $sCart;
    private $sGoods;
    private $sAddress;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->setView(Blue_Action::VIEW_SMARTY3);
        $this->sTransfers = new Service_Transfers();
        $this->sGoods = new Service_Goods();
        $this->sCarts = new Service_Carts();
        $this->sUser = new Service_User();
    }

    public function __execute()
    {
        if ($this->getRequest()->isGet()) {

            $session = $this->getSession();
            $id = !empty($_GET['id']) ?  intval($_GET['id']) : 0;
            if(empty($id))
            {
                $this->Warning('参数不能为空');
            }
            //订单信息
            $info = $this->sTransfers->getById($id);
            if(!empty($info['fromid']) && $info['fromid']!=$session['id']){
                $from=$this->sUser->getById($info['fromid']);
            }
            //订单商品
            $carts = $this->sCarts->getByOrderId($info['id']);
            foreach($carts as &$v)
            {
                $v['goods']=$this->sGoods->getById($v['goodsid']);
                $v['goods']['amount']=$v['amount'];
            }
            if($info['fromid']==0 && $info['state']==1){
                $state='交易成功';
            }
            if($info['fromid']!=0 && $info['fromid']!=$session['id']){
                if($info['audit']==0){
                    $state='上级尚未审核';
                }
                if($info['audit']==1){
                    $state='审核通过';
                }
                if($info['audit']==2){
                    $state='审核不通过';
                }
                if($info['state']==2){
                    $state='你已取消订单';
                }
                $status=0;
            }
            if($info['fromid']!=0 && $info['fromid']==$session['id']){
                
                if($info['audit']==0){
                    $state='您尚未审核';
                }
                if($info['audit']==1){
                    $state='审核通过';
                }
                if($info['audit']==2){
                    $state='审核不通过';
                }
                if($info['state']==2){
                    $state='你的下级已取消订单';
                }
                $status=1;
            }
            return array('data' => $info,'cart'=>$carts,'from'=>$from,'state'=>$state,'status'=>$status);
        }
    }
}
