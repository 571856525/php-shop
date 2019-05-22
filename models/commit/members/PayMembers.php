<?php
/**
 * 会员写入
 * User: Administrator
 * Date: 2016/3/9
 * Time: 18:05
 */
class Commit_Members_PayMembers extends Blue_Commit{
	private $dMembers;
	private $dPassport;
	protected function __register(){
		$this->transDB = array('pingwords');
	}
	protected function __prepare(){
		$this->dMembers = new Dao_Members();
		$this->dPassport = new Dao_Passport();
	}
	protected function __execute(){
		$req = $this->getRequest();
		//修改普通用户type   为会员  添加用户公司、职位
		$this->dPassport->update(sprintf('openid=%s', $req['openid']), array('type' => 1,'firm'=>$req['firm'],'position' =>$req['position']));
		//写入会员表
		$pass = $this->sPassport->getByOpen($req['openid']);//根据opid获取用户
		$mid = Arch_ID::g('members');
		//获取到期时间
		$time = time();
		date_default_timezone_set('PRC');
		$date = date('Y',$time) + 1 . '-' . date('m-d H:i:s');
		$time = strtotime($date);
		$mem = array(
			'id' =>$mid,
			'pass_id' => $pass['id'],
			'type' => 1,//会员级别
			'create_time' =>time(),
			'expir_time' => $time,//会员到期时间
			'tel' => $req['tel'],//用户手机号
		);
		$this->dMembers->insert($mem, true);
	}
}
