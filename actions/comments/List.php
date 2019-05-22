<?php

/**
 * 商品列表
 */

class Action_List extends App_Action
{
    private $sComments;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sComments = new Service_Comments();
        $this->sUser= new Service_User();
        $this->setView(Blue_Action::VIEW_SMARTY3);
    }

    public function __execute()
    {
        if ($this->getRequest()->isGet()) {
        	$id =!empty($_GET['id']) ? intval($_GET['id']) : 0;
            $pn = !empty($_GET['pn']) ?  intval($_GET['pn']) : 1;
            $rn = !empty($_GET['rn']) ?  intval($_GET['rn']) : 2;
            if(empty($id))
            {
                $this->Warning('ID不能为空');
            }
            $list = $this->sComments->getList($id,$pn,$rn);
            foreach ($list as &$value) {
                $value['create_time']=date('Y-m-d H:i:s',$value['create_time']);
                $user=$this->sUser->getById($value['userid']);
                $value['nickname']=$user['nickname'];
                $value['headimgurl']=$user['headimgurl'];
            }
            $count=count($list);
            return array('list' => $list, 'page' => Blue_Page::pageInfo($count, $pn, $rn));
       }
       //分页
       $this->setView(Blue_Action::VIEW_JSON);
       $ret=$this->verify();
       $rn=2;
       $pn=$ret['pn'];
       $list = $this->sComments->getList($ret['id'],$pn,$rn);
        foreach ($list as &$value) {
            $value['create_time']=date('Y-m-d H:i:s',$value['create_time']);
            $user=$this->sUser->getById($value['userid']);
            $value['nickname']=$user['nickname'];
            $value['headimgurl']=$user['headimgurl'];
        }
        $count=count($list);
        return array('list' => $list, 'page' => Blue_Page::pageInfo($count, $pn, $rn));

    }
    public function verify()
    {
        $rule = array(
            'id' => array('filterIntBetweenWithEqual', array(0)),
            'pn' => array('filterIntBetweenWithEqual', array(0)),
        );
        $req = Blue_Filter::filterArray($_POST, $rule);
        return $req;
    }

}
