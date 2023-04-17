<?php
/**
 * Controller Action Helper setup common function for session
 */
class My_Controller_Action_Helper_Session extends Zend_Controller_Action_Helper_Abstract
{
    
    // Session object
    protected $_session;

    // Session namespace
    const SESSION_NAMESPACE = 'MY_SESSION';

    public function __construct( )
    {
        $config = Zend_Registry::get('config');
        $config = $config['session'];

        $this->_session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        $times = SESSION_TIMEOUT;//in minute
        $this->_session->setExpirationSeconds( $times * 60 );
    }

    /**
     */
    public function setSession($key, $data)
    {
        $session = $this->_session;
        $session->$key = $data;
    }

    public function unsetSession( $key )
    {
        $session = $this->_session;
        $session->__unset( $key );
    }

    /**
     * Get Session
     */
    public function getSession($key)
    {
        $session = $this->_session;
        return $session->$key;
    }

    /**
     */
    public function detroySession()
    {
        $session = $this->_session;
        $session->unsetAll();
    }
}