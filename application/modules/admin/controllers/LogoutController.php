<?php
/**
 * Log
 *
 */
class Admin_LogoutController extends FrontBaseAction{
    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
    }

    /**
     * Index page
     */
    public function indexAction() {
        // Clear all session of browser
        Zend_Session::destroy();
        $this->_redirect( '/admin/auth/login/' );
    }
}
