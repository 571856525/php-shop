<?php

/**
 * 评论添加
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_Comments_Create extends Blue_Commit
{
    private $dComments;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dComments = new Dao_Comments();
        $this->dOrder = new Dao_Order();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        //添加
        if(!empty($req))
        {
            foreach ($req['ids'] as &$value) {
                core_log::debug($value);
                $data=array(
                    'goodsid'=>$value,
                    'content'=>$req['content'],
                    'userid'=>$req['userid'],
                    'img_url'=>$req['img_url'],
                    'create_time'=>time(),
                    'status'   => 1
                );
                $this->dComments->insert($data, true);
                $this->dOrder->update("id=".$req['orderid'], array('comments'=>1));
            }
        }
    }
}
