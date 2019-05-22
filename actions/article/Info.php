<?php

/**
 * 资讯信息
 */

class Action_Info extends App_Action
{
    private $sArticle;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        //$this->setView(Blue_Action::VIEW_JSON);
        $this->sArticle = new Service_Article();
        $this->setView(Blue_Action::VIEW_SMARTY2);
    }

    public function __execute()
    {
        $id =!empty($_GET['id']) ? intval($_GET['id']) : 0;
        if(empty($id))
        {
            $this->Warning('ID不能为空');
        }
        $data = $this->sArticle->getById($id);
        return array('data' => $data);
    }
}
