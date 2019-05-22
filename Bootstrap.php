<?php
 /**
  * 基础的Bootstrap文件
  *
  * @author 胡峰 <hufeng@yunsupport.com>
  * @version 1.0.0
*/
   class Bootstrap extends Yaf_Bootstrap_Abstract
   {
       public function _initView(Yaf_Dispatcher $dispatcher){
                 $dispatcher->autoRender(false);
       }

       public function _initLocal(Yaf_Dispatcher $dispatcher){
                 Yaf_Loader::getInstance()->registerLocalNamespace(array("App"));
       }

       public function _initRouter(Yaf_Dispatcher $dispatcher){
                 $dispatcher->catchException(true);
       }
  }
