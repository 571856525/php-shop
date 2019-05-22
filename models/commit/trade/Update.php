<?php

/**
 * 购物车添加修改
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */

class Commit_Trade_Update extends Blue_Commit
{
    private $dTrade;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dTrade= new Dao_Trade();
        $this->dAgent= new Dao_Agent();
        $this->dUser= new Dao_User();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        //修改
        $trade=$this->dTrade->selectOne('id='.$req['id'],'*');
        if($trade){
            $this->dTrade->update('id='.$req['id'],array('status'=>11));
            $this->dUser->update('id='.$trade['userid'],"amount=amount+{$trade['amount']}");
            $user=$this->dUser->selectOne('id='.$trade['userid'],'*');
            $weixin = new App_Weixin();
            $temp = Arch_Yaml::get('template');	
            $data_1 = array(
                'touser' => (string)$user['openid'],      
                'template_id' => $temp['user']['tid2'],     //模版id 
                'data' => array(
                    'first' => array('value' => '您好，您已经充值成功。'),
                    'keyword1' => array('value' => $trade['amount']),
                    'keyword2' => array('value' => date('Y-m-d H:i:s',$trade['create_time'])),
                    'keyword3' => array('value' => $user['amount']),
                    'remark' => array('value' => '感谢您对我们的信任，我们将为您提供更优质的服务。'),
                )
            );
            $json_1 = json_encode($data_1);
            $da1 = $weixin->setNotice($json_1);
        }
    }
}