<?php
/**
 * 音频上传
 * 
 */
class Action_Audio extends App_Action
{
	private $sUpload;
	public function __prepare(){
		$this->hoodNeedLogin = true;
		$this->setView(Blue_Action::VIEW_JSON);
		$this->sUpload = new Service_Upload();
	}
	
	public function __execute(){
		$file = $this->getFile();
		$fileInfo = file_get_contents($file);
		//获取时长
		$ffmpeg = new App_Ffmpeg();
		$time = $ffmpeg->getTime($file);
		/*
		$ins = Arch_Paf::instance('image');
		$data = array(
			'module' => 'cyps',
			'file' => $fileInfo
		);
		$r = $ins->call('audio/publish', $data);
		$url = $r['data'];
		 */
		$url = array();
		$url[0] = $this->sUpload->upload($file, 'mp3', 'video');
		return array('url' => $url, 'time' => $time);
	}
	
	public function __complete(){
		
	}
	
	public function getFile(){
		$file = $_FILES['file'];
		//判断音频
		if($file['error'] != UPLOAD_ERR_OK){
			throw new Blue_Exception_Warning("文件上传出错");
		}
		if($file['size'] > 1024 * 1024 * 80){
			throw new Blue_Exception_Warning("文件太大");
		}
		if(preg_match('/[mp3]$/', strtolower($file['name'])) == 0){
			throw new Blue_Exception_Warning("文件格式不对");
		}
		return $file['tmp_name'];
	}
}



