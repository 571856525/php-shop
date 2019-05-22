<?php
/**
 * 工具上传
 * 
 */
class Action_Tool extends App_Action
{
	public function __prepare(){
		$this->hoodNeedLogin = true;
		$this->sUpload = new Service_Upload();
		$this->setView(Blue_Action::VIEW_JSON);
	}
	
	public function __execute(){
		$file = $this->getFile();
		$url = $this->sUpload->upload($file['name'], $file['type']);
		return array('url' => $url);
	}
	
	public function __complete(){}
	
	public function getFile(){
		$file = $_FILES['file'];
		//判断
		if($file['error'] != UPLOAD_ERR_OK){
			throw new Blue_Exception_Warning("文件上传出错");
		}
		if($file['size'] > 1024 * 1024 * 1024){
			throw new Blue_Exception_Warning("文件太大");
		}
		if(preg_match('/[doc|txt|xlsx|xls|docx|pptx|ppt|zip|rar]$/', strtolower($file['name'])) == 0){
			throw new Blue_Exception_Warning("文件格式不对");
		}
		//获取文件类型
		$type = strrchr($file['name'], '.');
		$type = substr($type, 1);
		return array('name' => $file['tmp_name'], 'type' => $type);
	}
}

