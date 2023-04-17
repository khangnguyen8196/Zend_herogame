<?php

/**
 * Main page
 */
class Admin_IndexController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->loadJs(array( 'validation'));
        $this->view->headLink()->appendStylesheet($this->autorefresh->autoRefreshRewriter("/ad-min/assets/css/site/_dev.css"));
    }

    /**
     * Search page
     */
    public function indexAction() {
        $this->isLoggedIn();
    }

}
