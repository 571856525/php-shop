<?php
/**
 * 生成二维码的接口
 *
 *
 * @author hufeng(@yunsupport.com)
 * @version 1.0
 */
require_once(dirname(__FILE__) . '/qrcode/' . 'phpqrcode.php');

class App_Qrcode
{
	public static function create($text)
	{
		$url = Qrcode::png($text, $outfile = false, $level = QR_ECLEVEL_H, $size=7, $margin=1, $saveandprint=false);
		return $url;
	}
}

