<?php
/**
 * 专门为UE	editor提供的支持文件
 * 
 * @author hufeng<@yunsupport.com>
 * @copyright 2014~ (c) @yunsupport.com
 * @Time: Wed 12 Nov 2014 03:04:25 PM CST
 */
class Action_Ueditor extends App_Action
{
    private $sUpload;
    public function __prepare(){
    	$this->hoodNeedLogin = true;
        $this->sUpload = new Service_Upload();
        $this->setView(Blue_Action::VIEW_JSON);
    }
    public function __execute(){
        $action = $_GET['action'];
        if($action == 'config'){
            $CONFIG = array(
                'imageActionName' => 'upload',
                'imageFieldName' => 'file',
                'imageMaxSize' => 1024 * 1024 * 10,
                'imageAllowFiles' => array('.png', '.jpg', '.jpeg'),
                'imageCompressEnable' => true,
                'imageCompressBorder' => 800,
                'imageInsertAlign' => 'none',
                'imageUrlPrefix' => '',
                'imagePathFormat' => 'asdasdf',
            );
            echo json_encode($CONFIG);
            exit;
        }elseif('upload' == $action){
			$this->upload();
        }
    }
    public function __complete(){
    }
    public function upload(){
		$file = $this->getFile();
        $suffix = $this->getSuffix();
        $url = $this->sUpload->uploadImage($file, $suffix, 1024);
        $ret = array(
            'state' => 'SUCCESS',
            'url' => $url,
            'title' => '',
            'original' => '',
            'type' => $suffix,
            'size' => 102400
        );
        echo json_encode($ret);
        exit;
    }
    private function getFile(){
		$file = $_FILES['file'];
		if($file['error'] != UPLOAD_ERR_OK){
            throw new Blue_Exception_Warning("文件上传出错");
        }
        if($file['size'] > 1024 * 1024 * 20){
            throw new Blue_Exception_Warning("文件太大");
        }
        if(preg_match('/[jpg|jpeg|png|mp4]$/', strtolower($file['name'])) == 0){
            throw new Blue_Exception_Warning("文件格式不对");
        }
        return $file['tmp_name'];
    }
    private function getSuffix(){
        $name = $_FILES['file']['name'];
        return strtolower(substr($name, strrpos($name, '.') + 1));
    }
}
