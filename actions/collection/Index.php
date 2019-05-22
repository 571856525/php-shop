<?php

/**
 * 商品列表
 */

class Action_Index extends App_Action
{
    private $sCollection;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sCollection = new Service_Collection();
        $this->sGoods = new Service_Goods();
        $this->sGoodstype = new Service_Goodstype();
        $this->sUser= new Service_User();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $sess=$this->getSession();
        $user=$this->sUser->getById($sess['id']);
        $rn =!empty($_GET['rn']) ? intval($_GET['rn']) : 10;
        $pn =!empty($_GET['pn']) ? intval($_GET['pn']) : 1;
        $list = $this->sCollection->getList($sess['id'],$pn,$rn);
        foreach ($list as &$value) {
            $value['create_time']=date('Y-m-d H:i:s',$value['create_time']);
            $goods= $this->sGoods->getById($value['goodsid']);
            $value['goodsname']=$goods['goodsname'];
            $value['goodspic']=$goods['goodspic'];


            $goodstype=$this->sGoodstype->getById($goods['classid']);
            $value['amount']=$goodstype['user'.$user['reward'].'_price'];
        }
        $count= $this->sCollection->getCount($sess['id']);
        return array('list' => $list, 'page' => Blue_Page::pageInfo($count, $pn, $rn));
    }
}
