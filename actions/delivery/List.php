<?php

/**
 * 我的发货列表列表
 */

class Action_List extends App_Action
{
    private $sDelivery;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sDelivery = new Service_Delivery();
        $this->sUser= new Service_User();
        $this->sGoods= new Service_Goods();
        $this->sAddress= new Service_Address();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $sess=$this->getSession();
        if ($this->getRequest()->isGet()) {
            $pn = !empty($_GET['pn']) ?  intval($_GET['pn']) : 1;
            $rn = !empty($_GET['rn']) ?  intval($_GET['rn']) : 6;
            $list = $this->sDelivery->getList($sess['id'],$pn,$rn);
            foreach ($list as &$value) {
                $value['addtime']=date('Y-m-d H:i:s',$value['addtime']);
                $commodity=json_decode($value['goods']);
                foreach($commodity as &$v){
                    $v=get_object_vars($v);
                    $goods=$this->sGoods->getInfo($v['id']);            
                    $v['goodsname']=$goods['goodsname'];
                    $v['goodspic']=$goods['goodspic'];  
                }
                $value['goods']=$commodity;
                $value['contact'] = $this->sAddress->getById($value['addressid'])['contact']; 
            }
            $count=count($list);
            return array('list' => $list, 'page' => Blue_Page::pageInfo($count, $pn, $rn));
       }
       //分页
       $this->setView(Blue_Action::VIEW_JSON);
       $ret=$this->verify();
       $rn=$ret['rn'];
       $pn=$ret['pn'];
       $list = $this->sDelivery->getList($sess['id'],$pn,$rn);
        foreach ($list as &$value) {
            $value['addtime']=date('Y-m-d H:i:s',$value['addtime']);
            $commodity=json_decode($value['goods']);
            foreach($commodity as &$v){
                $v=get_object_vars($v);
                $goods=$this->sGoods->getInfo($v['id']);            
                $v['goodsname']=$goods['goodsname'];
                $v['goodspic']=$goods['goodspic'];  
            }
            $value['goods']=$commodity;
        }
        $count=count($list);
        return array('list' => $list, 'page' => Blue_Page::pageInfo($count, $pn, $rn));

    }
    public function verify()
    {
        $rule = array(
            'pn' => array('filterIntBetweenWithEqual', array(0)),
            'rn' => array('filterIntBetweenWithEqual', array(0)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }

}
