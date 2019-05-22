<?php
/**
 * 默认的错误处理环节
 *
 * @author hufeng(@yunbix.com)
 * @copyright (c) 2015-2016 yunbix.com
 */
class Controller_Error extends Yaf_Controller_Abstract
{
    public function errorAction(){
        $exp = $this->getRequest()->getException();
        $code = $exp->getCode();
        $ini = Arch_Yaml::get('global', NULL, true);
        if($ini['debug']){
            printf("未捕获的异常:%s", $exp->getMessage());
            return true;
        }   
        if($code === YAF_ERR_NOTFOUND_CONTROLLER || $code === YAF_ERR_NOTFOUND_ACTION){
            Core_Log::warning($exp->getMessage());
            header('HTTP/1.1 403 Forbidden');
        }else{
            Core_Log::fatal($exp->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
        }   
    }   
}  