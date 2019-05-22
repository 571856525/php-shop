<?php
/**
 * 视频上传
 * 
 */
class Action_Video extends App_Action
{
	public function __prepare(){
		$this->hoodNeedLogin = true;
		$this->setView(Blue_Action::VIEW_JSON);
		$this->sUpload = new Service_Upload();
	}
	
	public function __execute(){
		$file = $this->getFile();
		/*$fileInfo = file_get_contents($file);
		//生成缩略图片
		$ffmpeg = new App_Ffmpeg();
		$image = $ffmpeg->getImage($file);
		$ins = Arch_Paf::instance('image');
		//上传缩略图
		$data = array(
			'module' => 'cyps',
			'file' => $image['image']
		);
		$r = $ins->call('publish', $data);
		$imgurl = $r['data'];
		//上传视频
		/*$data = array(
			'module' => 'cyps',
			'file' => $fileInfo
		);
		$r = $ins->call('video/publish', $data);
		$url = $r['data'];*/
		//获取视频时间
		$ffmpeg = new App_Ffmpeg();
		$time = $ffmpeg->getTime($file);
		$imgurl = 'http://img1.yunbix.com/cyps/images/befd785a6cf9a62b1a9815a57a35628b_3.jpg';
		$url[0] = $this->sUpload->upload($file, 'mp4', 'video');
		return array('url' => $url, 'imgurl' => $imgurl, 'time' => $time);
	}
	
	public function __complete(){
		
	}
	
	public function getFile(){
		$file = $_FILES['file'];
		//判断音频
		if($file['error'] != UPLOAD_ERR_OK){
			throw new Blue_Exception_Warning("文件上传出错");
		}
		if($file['size'] > 1024 * 1024 * 1024){
			throw new Blue_Exception_Warning("文件太大");
		}
		if(preg_match('/[mp4|mpg]$/', strtolower($file['name'])) == 0){
			throw new Blue_Exception_Warning("文件格式不对");
		}
		return $file['tmp_name'];
	}
}



