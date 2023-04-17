<?php


class My_Controller_Plugin_ModularAuth extends Zend_Controller_Plugin_Abstract
{
    protected $groups;


    public function __construct($groups = array())
    {
        $this->groups = array();

        // make sure $this->groups will be in format array of arrays
        // see self::getModuleMemberName()
        foreach ((array) $groups as $id => $modules) {
            $this->groups[$id] = (array) $modules;
        }
    }


    public function getModuleMemberName($moduleName)
    {
        $member = Zend_Auth_Storage_Session::MEMBER_DEFAULT;

        // try to find group of module
        foreach ($this->groups as $id => $modules) {
            if (in_array($moduleName, $modules)) {
                // return group's member name
                return $member . $id;
            }
        }

        // return fallback member name
        return $member;
    }


    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $namespace = Zend_Auth_Storage_Session::NAMESPACE_DEFAULT;
        $member    = $this->getModuleMemberName($request->getModuleName());
        $storage   = new Zend_Auth_Storage_Session($namespace, $member);
        Zend_Auth::getInstance()->setStorage($storage); 
    }
}
