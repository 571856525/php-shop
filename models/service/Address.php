<?php
	
	class Service_Address
	{
		private $dAddress;
		
		public function __construct ()
		{
			$this->dAddress = new Dao_Address();
		}
		//根据ID获取收货地址信息
		public function getById ($id)
		{
			return $this->dAddress->getById($id);
		}

		//根据id获取收货地址列表
		public function getListByid ($id)
		{
			return $this->dAddress->getListByid($id);
		}	
	}
