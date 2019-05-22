<?php
/**
 * 上传相关
 */
class Controller_Upload extends Yaf_Controller_Abstract
{
	public $actions = array(
		'ueditor' => 'actions/upload/Ueditor.php',      //文本图片上传
		'audio' => 'actions/upload/Audio.php',          //音频上传
		'video' => 'actions/upload/Video.php',          //音频上传
		'tool' => 'actions/upload/Tool.php',            //工具上传
		'image' => 'actions/upload/Image.php',            //图片上传
	);
}
