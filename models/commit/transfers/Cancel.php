<?php

/**
 * 取消订单
 */

class Commit_Transfers_Cancel extends Blue_Commit
{
	private $dTransfers;
	
    protected function __register()
    {
        $this->transDB = array('shop');
    }
    protected function __prepare()
    {
        $this->dTransfers = new Dao_Transfers();
        $this->dCarts = new Dao_Carts();
        $this->dWarehouse = new Dao_Warehouse();
        $this->dUser = new Dao_User();
        $this->dRecord = new Dao_Record();
    }
    protected function __execute()
    {
        $req = $this->getRequest();
        $transfers=$this->dTransfers->getById($req['id']);
        $this->dTransfers->update('id='.$req['id'],array('state'=>2));
        $user=$this->dUser->getById($transfers['userid']);
        //余额返回
        $transfers['amount']+=$transfers['real_amount'];
        $this->dUser->update('id='.$transfers['userid'],"amount=amount+{$transfers['amount']}");
        $data=array(
            'userid'=>$transfers['userid'],
            'subid'=>$transfers['fromid'],
            'amount'=> $transfers['amount'],
            'ordersn'=> $transfers['ordersn'],
            'addtime'=>time(),
            'content'=>'你已取消订单，金额退到你的余额中了',
            'type'=>3
        );
        $this->dRecord->insert($data,true);

        $weixin=new App_Weixin();
        $temp = Arch_Yaml::get('template');
        core_log::debug($user['openid']);	
        $data = array(
            'touser' => (string)$user['openid'],     //openid
            'template_id' => $temp['order']['tid5'],     //模版id 
            'topcolor' => '#FF0000',
            'data' => array(
                'first' => array('value' => '您好，您有一个订单已成功取消。'),
                'keyword1' => array('value' => $transfers['ordersn']),
                'keyword2' => array('value' => date('Y-m-d H:i:s',time())),
                'remark' => array('value' => '感谢您的支持'),
            )
        );
        $json = json_encode($data);
        $weixin->setNotice($json);

    }
}
