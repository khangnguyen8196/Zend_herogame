<?php

/**
 * Error Controller
 */
class Site_ErrorController extends Zend_Controller_Action {

    public function errorAction() {
        $this->_redirect("/");
    }

    public function indexAction() {
        $this->_redirect("/");
    }

}
