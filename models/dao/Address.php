<?php
	
	
	class Dao_Address extends Blue_Dao
	{
		public function __construct ()
		{
			parent::__construct('shop', 'shop', 'address');
		}
		//根据ID获取收货地址信息
		public function getById ($id)
		{
			return $this->selectOne(sprintf('id=%d', $id), '*');
		}
		//根据id获取收货地址列表
		public function getListByid ($id)
		{
			return $this->select(sprintf('status=1 and userid=%d',$id), '*','order by id desc ');
		}		
	}
