<?php

/**
 * 商品列表
 */

class Action_Index extends App_Action
{
    private $sGoods;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sGoods = new Service_Goods();
        $this->setView(Blue_Action::VIEW_JSON);
        //$this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        $classid =!empty($_GET['classid']) ? intval($_GET['classid']) : 0;
        $rn =!empty($_GET['rn']) ? intval($_GET['rn']) : 10;
        $pn =!empty($_GET['pn']) ? intval($_GET['pn']) : 1;

        $data = $this->sGoods->getList($classid,$pn,$rn);
        $count= $this->sGoods->getCount($classid);
        return array('data' => $data, 'page' => Blue_Page::pageInfo($count, $pn, $rn));
    }
}
