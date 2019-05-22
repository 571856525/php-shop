<?php
/**
 * 图片上传
 */
class Action_Image extends App_Action
{
	public function __prepare(){
		$this->hoodNeedLogin = true;
		$this->setView(Blue_Action::VIEW_JSON);
	}
	
	public function __execute(){
		$t22 = microtime(true);
		$file = $this->getFile();
		$fileInfo = file_get_contents($file);//打开文件
		 core_log::debug('111111111111---'.$fileInfo);
		$ins = Arch_Paf::instance('image');
		$data = array(
			'module' => 'cyps',
			'file' => $fileInfo
		);
		$r = $ins->call('publish', $data);
		$url = $r['data'];
		$t222 = microtime(true);
		core_log::debug('111111111111---'.$avatar.'-----上传-----imagick----耗时'.round($t22-$t222,3).'秒');
		return array('url' => $url);
	}
	
	public function __complete(){
	
	}
	
	public function getFile(){
		$file = $_FILES['file'];
		//判断音频
		if($file['error'] != UPLOAD_ERR_OK){
			throw new Blue_Exception_Warning("文件上传出错");
		}
		if($file['size'] > 1024 * 1024 * 50){
			throw new Blue_Exception_Warning("文件太大");
		}
		if(preg_match('/[jpg|jpeg|png]$/', strtolower($file['name'])) == 0){
			throw new Blue_Exception_Warning("文件格式不对");
		}
		return $file['tmp_name'];
	}
}
