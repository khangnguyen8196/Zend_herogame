<?php
class My_Loader implements Zend_Loader_Autoloader_Interface
{
    public function autoload($class)
    {
        $dir = APPLICATION_PATH."/models/";
        $resources = $dir."resources";
        $utils = $dir."utils";
        $service = $dir."service";
        
        Zend_Loader::loadClass($class, array($resources, $service, $utils));
    }
}